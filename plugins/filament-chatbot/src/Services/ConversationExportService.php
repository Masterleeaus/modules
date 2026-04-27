<?php

namespace App\Extensions\Chatbot\System\Services;

use App\Extensions\Chatbot\System\Models\ChatbotConversation;
use App\Extensions\Chatbot\System\Models\ChatbotHistory;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Response;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;

class ConversationExportService
{
    public function export(ChatbotConversation $conversation, string $format = 'txt'): SymfonyResponse
    {
        $messages = $conversation->widgetHistories()
            ->orderBy('id')
            ->get();

        return match ($format) {
            'csv'   => $this->exportCsv($conversation, $messages),
            'pdf'   => $this->exportPdf($conversation, $messages),
            'json'  => $this->exportJson($conversation, $messages),
            default => $this->exportTxt($conversation, $messages),
        };
    }

    private function exportTxt(ChatbotConversation $conversation, Collection $messages): SymfonyResponse
    {
        $content = '';

        foreach ($messages as $message) {
            $role = strtoupper($message->role);
            $timestamp = $message->created_at?->format('Y-m-d H:i:s') ?? '';
            $content .= "[{$timestamp}] [{$role}] " . $message->message . PHP_EOL . PHP_EOL;
        }

        $fileName = "conversation-{$conversation->id}.txt";

        return Response::make($content, 200, [
            'Content-Type'        => 'text/plain',
            'Content-Disposition' => "attachment; filename=\"{$fileName}\"",
        ]);
    }

    private function exportCsv(ChatbotConversation $conversation, Collection $messages): SymfonyResponse
    {
        $handle = fopen('php://temp', 'r+');

        fputcsv($handle, ['Timestamp', 'Role', 'Message']);

        foreach ($messages as $message) {
            fputcsv($handle, [
                $message->created_at?->format('Y-m-d H:i:s') ?? '',
                strtoupper($message->role),
                $message->message,
            ]);
        }

        rewind($handle);
        $content = stream_get_contents($handle);
        fclose($handle);

        $fileName = "conversation-{$conversation->id}.csv";

        return Response::make($content, 200, [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$fileName}\"",
        ]);
    }

    private function exportPdf(ChatbotConversation $conversation, Collection $messages): SymfonyResponse
    {
        $chatbot = $conversation->chatbot;

        $pdf = app('dompdf.wrapper');
        $pdf->loadView('chatbot::export.pdf', [
            'chatbot'      => $chatbot,
            'conversation' => $conversation,
            'messages'     => $messages,
        ]);

        return $pdf->download("conversation-{$conversation->id}.pdf");
    }

    private function exportJson(ChatbotConversation $conversation, Collection $messages): SymfonyResponse
    {
        $data = [
            'conversation_id' => $conversation->id,
            'chatbot'         => $conversation->chatbot?->title ?? 'Chatbot',
            'exported_at'     => now()->toIso8601String(),
            'messages'        => $messages->map(fn (ChatbotHistory $message) => [
                'role'       => $message->role,
                'message'    => $message->message,
                'media_url'  => $message->media_url,
                'media_name' => $message->media_name,
                'timestamp'  => $message->created_at?->toIso8601String(),
            ])->toArray(),
        ];

        $content = json_encode($data, JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE);
        $fileName = "conversation-{$conversation->id}.json";

        return Response::make($content, 200, [
            'Content-Type'        => 'application/json',
            'Content-Disposition' => "attachment; filename=\"{$fileName}\"",
        ]);
    }
}
