<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class BackupController extends Controller
{
    public function index()
    {
        return view('admin.backup.index');
    }

    public function downloadDatabase()
    {
        $database = config('database.connections.mysql.database');

        try {
            $tables = DB::select('SHOW TABLES');
            $tableKey = 'Tables_in_' . $database;
            $sql = "-- Backup Database: {$database}\n-- Tanggal: " . date('Y-m-d H:i:s') . "\n\n";
            $sql .= "SET FOREIGN_KEY_CHECKS=0;\n\n";

            foreach ($tables as $table) {
                $tableName = $table->$tableKey;

                $createTable = DB::select("SHOW CREATE TABLE `{$tableName}`");
                $sql .= "DROP TABLE IF EXISTS `{$tableName}`;\n";
                $sql .= $createTable[0]->{'Create Table'} . ";\n\n";

                $rows = DB::table($tableName)->get();
                foreach ($rows as $row) {
                    $rowArray = (array) $row;
                    $columns = array_keys($rowArray);
                    $values = array_values($rowArray);

                    $escapedValues = array_map(function ($value) {
                        return is_null($value) ? 'NULL' : "'" . addslashes($value) . "'";
                    }, $values);

                    $sql .= "INSERT INTO `{$tableName}` (`" . implode('`, `', $columns) . "`) VALUES (" . implode(', ', $escapedValues) . ");\n";
                }
                $sql .= "\n";
            }

            $sql .= "SET FOREIGN_KEY_CHECKS=1;\n";

            $filename = 'backup-' . $database . '-' . date('Y-m-d_H-i-s') . '.sql';
            $headers = [
                'Content-Type' => 'application/sql',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            ];

            return response()->stream(function () use ($sql) {
                echo $sql;
            }, 200, $headers);

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal melakukan backup database: ' . $e->getMessage());
        }
    }

    /**
     * Proses Restore Database dari file .sql yang diunggah
     */
    public function restoreDatabase(Request $request)
    {
        $request->validate([
            'backup_file' => 'required|file|mimes:sql,txt|max:10240', // Maksimal 10MB
        ]);

        try {
            $file = $request->file('backup_file');
            $sqlContent = File::get($file->getRealPath());

            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
            DB::unprepared($sqlContent);
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');

            return redirect()->back()->with('success', 'Database berhasil direstore/dipulihkan dengan sukses!');

        } catch (\Exception $e) {
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');
            return redirect()->back()->with('error', 'Gagal merestore database. Pastikan format file .sql valid. Detail: ' . $e->getMessage());
        }
    }

    /**
     * Unggah & Ekstrak file ZIP pembaruan source code langsung ke server
     */
    public function restoreSourceZip(Request $request)
    {
        $request->validate([
            'update_zip' => 'required|file|mimes:zip|max:102400', // Maksimal 100MB
        ]);

        try {
            $file = $request->file('update_zip');
            $zip = new \ZipArchive;

            if ($zip->open($file->getRealPath()) === TRUE) {
                // Mengekstrak langsung ke root direktori project Laravel
                $destinationPath = base_path();

                $zip->extractTo($destinationPath);
                $zip->close();

                // Bersihkan cache agar perubahan kodingan langsung terbaca sistem
                \Artisan::call('optimize:clear');

                return redirect()->back()->with('success', 'Source code pembaruan berhasil diunggah dan diterapkan!');
            }

            return redirect()->back()->with('error', 'Gagal membaca file arsip ZIP.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal mengekstrak pembaruan: ' . $e->getMessage());
        }
    }
}
