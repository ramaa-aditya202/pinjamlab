<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WebhookController extends Controller
{
    /**
     * Handle webhook untuk notifikasi peminjaman ke n8n
     */
    public function bookingNotification(Request $request)
    {
        try {
            $n8nWebhookUrl = config('services.n8n.webhook_url');
            
            if (!$n8nWebhookUrl) {
                Log::error('N8N webhook URL not configured');
                return response()->json(['error' => 'Webhook URL not configured'], 500);
            }

            $requestData = $request->all();
            Log::info('Sending booking notification to n8n', [
                'event_type' => $requestData['event_type'] ?? 'unknown',
                'booking_id' => $requestData['booking_id'] ?? 'unknown'
            ]);

            // Kirim data ke n8n
            $response = Http::timeout(10)->post($n8nWebhookUrl, $requestData);

            if ($response->successful()) {
                Log::info('Booking notification sent to n8n successfully', [
                    'status_code' => $response->status(),
                    'event_type' => $requestData['event_type'] ?? 'unknown'
                ]);
                return response()->json(['success' => true]);
            } else {
                Log::error('Failed to send booking notification to n8n', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                    'event_type' => $requestData['event_type'] ?? 'unknown'
                ]);
                return response()->json(['error' => 'Failed to send notification'], 500);
            }

        } catch (\Exception $e) {
            Log::error('Error sending booking notification to n8n: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json(['error' => 'Internal server error'], 500);
        }
    }
}
