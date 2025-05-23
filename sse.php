<?php
// Configuração inicial
$step = (int) $_GET['max'] ?? 5;  // Recebe o número máximo de passos via GET
date_default_timezone_set("America/Sao_Paulo"); // Configura fuso horário
set_time_limit(0); // Remove limite de execução do script. Necessário, já que, em geral são processos custosos em termos de tempo de execução.
header("X-Accel-Buffering: no"); // Desabilita buffering no Nginx
header("Content-Type: text/event-stream"); // Define tipo de conteúdo para SSE
header("Cache-Control: no-cache"); // Impede cacheamento da resposta

try {
    // Loop principal de processamento
    for ($i = 0; $i <= $step; $i++) {
        // Exemplo de tratamento de erro (descomentar para testar)
        // if ($i === 3) {
        //     throw new Exception('Erro simulado!');
        // }

        // Monta dados do passo atual
        $data = [
            'step' => $i,
            'done' => false,
            'success' => null,
            'message' => "Passo $i de $step",
        ];

        // Formata dados conforme especificação SSE
        echo 'data: ';
        echo json_encode($data);
        echo PHP_EOL; // Nova linha obrigatória
        echo PHP_EOL; // Segunda nova linha indica fim do evento

        // Verifica se cliente desconectou
        if (connection_aborted()) break;

        // Limpa buffers de saída
        while (ob_get_level() > 0) {
            ob_end_flush();
        }
        flush(); // Força envio imediato dos dados ao cliente
        sleep(1); // Pausa entre atualizações (para fins de simulação)
    }

    // Envio final após conclusão bem sucedida
    $data = [
        'step' => $step,
        'done' => true,
        'success' => true,
        'message' => "Processo finalizado!",
    ];
    echo 'data: ';
    echo json_encode($data);
    echo PHP_EOL;
    echo PHP_EOL;
} catch (Exception $e) {
    // Tratamento de erros
    $data = [
        'step' => $i,
        'done' => true,
        'success' => false,
        'message' => $e->getMessage(),
    ];
    echo 'data: ';
    echo json_encode($data);
    echo PHP_EOL;
    echo PHP_EOL;
}
