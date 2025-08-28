<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\LabBooking;

class CleanupRejectedBookings extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cleanup:rejected-bookings {--dry-run : Show what would be deleted without actually deleting}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clean up old rejected bookings that may cause unique constraint issues';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $isDryRun = $this->option('dry-run');
        
        $this->info('🔍 Mencari peminjaman yang ditolak...');
        
        $rejectedBookings = LabBooking::where('status', 'rejected')->get();
        
        if ($rejectedBookings->isEmpty()) {
            $this->info('✅ Tidak ada peminjaman yang ditolak ditemukan.');
            return 0;
        }
        
        $this->table(
            ['ID', 'User', 'Day', 'Hour', 'Subject', 'Status', 'Created'],
            $rejectedBookings->map(function ($booking) {
                return [
                    $booking->id,
                    $booking->user->name ?? 'Unknown',
                    $booking->day,
                    $booking->hour,
                    $booking->subject,
                    $booking->status,
                    $booking->created_at->format('Y-m-d H:i')
                ];
            })
        );
        
        $this->warn("📊 Ditemukan {$rejectedBookings->count()} peminjaman yang ditolak.");
        
        if ($isDryRun) {
            $this->info('🔍 DRY RUN - Tidak ada data yang dihapus.');
            $this->line('Jalankan tanpa --dry-run untuk menghapus data.');
            return 0;
        }
        
        if ($this->confirm('❓ Hapus semua peminjaman yang ditolak? (Recommended untuk menghindari constraint issues)')) {
            $deletedCount = LabBooking::where('status', 'rejected')->delete();
            
            $this->info("✅ Berhasil menghapus {$deletedCount} peminjaman yang ditolak.");
            $this->line('Sekarang slot-slot tersebut bisa diajukan peminjaman ulang.');
        } else {
            $this->info('❌ Pembersihan dibatalkan.');
        }
        
        return 0;
    }
}
