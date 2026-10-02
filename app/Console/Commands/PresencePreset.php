<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\ClientPresetService;
use App\Services\ModuleEntitlementService;
use function Laravel\Prompts\select;
use function Laravel\Prompts\confirm;

class PresencePreset extends Command
{
    protected $signature = 'presence:preset 
                            {preset_code? : Kode preset target (A, B, C, D, E, FULL)}
                            {--list : Tampilkan daftar preset yang tersedia}
                            {--yes : Otomatis konfirmasi tanpa tanya}';

    protected $description = 'Pindah preset konfigurasi klien (A, B, C, D, E, FULL) secara instan dan standar';

    protected array $presetPackageMap = [
        'A'    => ['package' => 'FNB_SMALL', 'addons' => []],
        'B'    => ['package' => 'OFFICE_STANDARD', 'addons' => []],
        'C'    => ['package' => 'RETAIL_SMALL', 'addons' => []],
        'D'    => ['package' => 'OFFICE_STANDARD', 'addons' => ['reimbursement']],
        'E'    => ['package' => 'FULL_HR', 'addons' => []],
        'F'    => ['package' => 'FULL_HR', 'addons' => ['reimbursement', 'loans']],
        'FULL' => ['package' => 'FULL_HR', 'addons' => ['reimbursement', 'loans']],
    ];

    public function handle(ClientPresetService $presetService, ModuleEntitlementService $entitlementService): int
    {
        $presets = $presetService->getPresets();

        // 1. Opsi --list
        if ($this->option('list')) {
            $this->showPresetList($presets);
            return 0;
        }

        $code = $this->argument('preset_code');
        $fromDropdown = false;

        // Jika tidak diberi argumen, tampilkan dropdown pilihan interaktif dengan keterangan lengkap
        if (!$code) {
            $fromDropdown = true;
            $options = [];
            foreach ($presets as $key => $p) {
                $options[$key] = sprintf(
                    "[%-4s] %s (%s) — %s",
                    $key,
                    $p['name'],
                    $p['badge'],
                    $p['subtitle']
                );
            }

            if (function_exists('Laravel\Prompts\select')) {
                $code = select(
                    label: 'Pilih Preset Konfigurasi Klien yang ingin diterapkan:',
                    options: $options,
                    default: 'A',
                    hint: 'Gunakan panah atas/bawah (↑ / ↓) lalu tekan Enter untuk langsung menerapkan'
                );
            } else {
                $this->showPresetList($presets);
                $code = $this->choice('Pilih preset yang ingin diterapkan', array_keys($presets), 0);
            }
        }

        $code = strtoupper(trim($code));
        if ($code === 'F') {
            $code = 'FULL';
        }

        if (!isset($presets[$code])) {
            $this->error("Error: Preset '{$code}' tidak valid. Pilihan yang tersedia: A, B, C, D, E, FULL.");
            return 1;
        }

        $preset = $presets[$code];
        $mapping = $this->presetPackageMap[$code] ?? ['package' => 'CUSTOM', 'addons' => []];

        $this->newLine();
        $this->line('<fg=cyan;options=bold>========================================================================</>');
        $this->line("<fg=white;options=bold>   MENERAPKAN PRESET {$code} : {$preset['name']}   </>");
        $this->line('<fg=cyan;options=bold>========================================================================</>');
        $this->line("  Tipe Bisnis    : <fg=yellow;options=bold>{$preset['badge']}</>");
        $this->line("  Paket Dasar    : <fg=white;options=bold>{$mapping['package']}</>");
        $this->line("  Deskripsi      : {$preset['subtitle']}");
        $this->newLine();

        // Jika dipilih dari dropdown atau ada flag --yes, langsung eksekusi tanpa tanya lagi
        if (!$this->option('yes') && !$fromDropdown) {
            if (function_exists('Laravel\Prompts\confirm')) {
                $confirmed = confirm(
                    label: "Terapkan {$preset['name']} sekarang?",
                    default: true,
                    yes: 'Ya, terapkan sekarang',
                    no: 'Batal'
                );
            } else {
                $confirmed = $this->confirm("Terapkan Preset {$code} sekarang?", true);
            }

            if (!$confirmed) {
                $this->warn("Operasi dibatalkan.");
                return 0;
            }
        }

        $this->info("Menyesuaikan modul, hak akses, dan pengaturan Preset {$code}...");

        // 1. Terapkan paket dasar agar entitlement sesuai
        $entitlementService->applyPackageChange(
            $mapping['package'],
            $mapping['addons'],
            null,
            "Developer CLI: apply preset {$code}"
        );

        // 2. Terapkan konfigurasi preset ke database
        $presetService->applyPreset($code);

        $this->newLine();
        $this->line('<fg=green;options=bold>========================================================================</>');
        $this->line("<fg=green;options=bold>✔ PRESET {$code} BERHASIL DITERAPKAN!</>");
        $this->line('<fg=green;options=bold>========================================================================</>');
        $this->line("  Nama Preset   : <fg=white;options=bold>{$preset['name']}</>");
        $this->line("  Status Preset : <fg=bright-green;options=bold>Preset {$code} Aktif</>");
        $this->line("  Data Klien    : <fg=green>100% aman (tidak ada data yang dihapus)</>");
        $this->newLine();

        return 0;
    }

    protected function showPresetList(array $presets): void
    {
        $this->newLine();
        $this->line('<fg=cyan;options=bold>DAFTAR PRESET STANDAR YANG TERSEDIA:</>');
        $this->line('------------------------------------------------------------------------');
        foreach ($presets as $key => $p) {
            $this->line("  <fg=yellow;options=bold>[{$key}]</> <fg=white;options=bold>{$p['name']}</> (<fg=green>{$p['badge']}</>)");
            $this->line("      {$p['subtitle']}");
        }
        $this->line('------------------------------------------------------------------------');
        $this->newLine();
    }
}
