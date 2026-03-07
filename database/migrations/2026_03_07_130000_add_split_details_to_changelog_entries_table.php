<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('changelog_entries', function (Blueprint $table) {
            if (!Schema::hasColumn('changelog_entries', 'details_public')) {
                $table->longText('details_public')->nullable()->after('details');
            }

            if (!Schema::hasColumn('changelog_entries', 'details_admin')) {
                $table->longText('details_admin')->nullable()->after('details_public');
            }
        });

        DB::table('changelog_entries')
            ->select('id', 'audience', 'details', 'details_public', 'details_admin')
            ->orderBy('id')
            ->chunkById(200, function ($rows): void {
                foreach ($rows as $row) {
                    $existingPublic = $this->cleanText($row->details_public);
                    $existingAdmin = $this->cleanText($row->details_admin);

                    if ($existingPublic !== null || $existingAdmin !== null) {
                        continue;
                    }

                    [$publicPart, $adminPart] = $this->splitLegacyDetails((string) ($row->details ?? ''));

                    $legacy = $this->cleanText((string) ($row->details ?? ''));
                    if ($legacy !== null) {
                        if ($publicPart === null && in_array($row->audience, ['public', 'both'], true)) {
                            $publicPart = $legacy;
                        }

                        if ($adminPart === null && in_array($row->audience, ['admin', 'both'], true)) {
                            $adminPart = $legacy;
                        }
                    }

                    DB::table('changelog_entries')
                        ->where('id', $row->id)
                        ->update([
                            'details_public' => $publicPart,
                            'details_admin' => $adminPart,
                        ]);
                }
            });
    }

    public function down(): void
    {
        Schema::table('changelog_entries', function (Blueprint $table) {
            if (Schema::hasColumn('changelog_entries', 'details_admin')) {
                $table->dropColumn('details_admin');
            }

            if (Schema::hasColumn('changelog_entries', 'details_public')) {
                $table->dropColumn('details_public');
            }
        });
    }

    private function splitLegacyDetails(string $details): array
    {
        $details = trim($details);
        if ($details === '') {
            return [null, null];
        }

        $publicPart = null;
        $adminPart = null;

        if (preg_match('/Public:\s*(.+?)(?:\nAdmin:|\nQuelle:|$)/s', $details, $match)) {
            $publicPart = $this->cleanText($match[1] ?? null);
        }

        if (preg_match('/Admin:\s*(.+?)(?:\nPublic:|\nQuelle:|$)/s', $details, $match)) {
            $adminPart = $this->cleanText($match[1] ?? null);
        }

        return [$publicPart, $adminPart];
    }

    private function cleanText(?string $text): ?string
    {
        $value = trim((string) $text);
        return $value === '' ? null : $value;
    }
};
