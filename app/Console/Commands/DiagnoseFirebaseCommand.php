<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\FirebaseService;
use Illuminate\Support\Facades\Log;
use Kreait\Firebase\Factory;

class DiagnoseFirebaseCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'firebase:diagnose {--fix : Attempt to fix common issues}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Diagnose Firebase configuration issues';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔍 Diagnosing Firebase configuration...');
        
        $issues = [];
        $fixes = [];
        
        // Check 1: Service account file exists
        $this->info('1. Checking service account file...');
        $serviceAccountPath = storage_path('firebase/glint-up-firebase-adminsdk-fbsvc-f7a7261316.json');
        
        if (!file_exists($serviceAccountPath)) {
            $issues[] = 'Service account file not found at: ' . $serviceAccountPath;
            $this->error('❌ Service account file not found');
        } else {
            $this->info('✅ Service account file exists');
            
            // Check 2: File is readable
            if (!is_readable($serviceAccountPath)) {
                $issues[] = 'Service account file is not readable';
                $this->error('❌ Service account file is not readable');
            } else {
                $this->info('✅ Service account file is readable');
            }
        }
        
        // Check 3: Valid JSON
        $this->info('2. Validating JSON structure...');
        if (file_exists($serviceAccountPath)) {
            $content = file_get_contents($serviceAccountPath);
            $json = json_decode($content, true);
            
            if (json_last_error() !== JSON_ERROR_NONE) {
                $issues[] = 'Invalid JSON in service account file: ' . json_last_error_msg();
                $this->error('❌ Invalid JSON structure');
            } else {
                $this->info('✅ Valid JSON structure');
                
                // Check 4: Required fields
                $this->info('3. Checking required fields...');
                $requiredFields = ['type', 'project_id', 'private_key_id', 'private_key', 'client_email', 'client_id'];
                $missingFields = [];
                
                foreach ($requiredFields as $field) {
                    if (!isset($json[$field]) || empty($json[$field])) {
                        $missingFields[] = $field;
                    }
                }
                
                if (!empty($missingFields)) {
                    $issues[] = 'Missing required fields: ' . implode(', ', $missingFields);
                    $this->error('❌ Missing required fields: ' . implode(', ', $missingFields));
                } else {
                    $this->info('✅ All required fields present');
                }
                
                // Check 5: Private key format
                if (isset($json['private_key'])) {
                    if (!str_contains($json['private_key'], '-----BEGIN PRIVATE KEY-----')) {
                        $issues[] = 'Invalid private key format';
                        $this->error('❌ Invalid private key format');
                    } else {
                        $this->info('✅ Valid private key format');
                    }
                }
            }
        }
        
        // Check 6: Network connectivity
        $this->info('4. Testing network connectivity...');
        $testUrl = 'https://oauth2.googleapis.com/token';
        $context = stream_context_create([
            'http' => [
                'timeout' => 10,
                'method' => 'HEAD'
            ]
        ]);
        
        $headers = @get_headers($testUrl, 1, $context);
        if ($headers === false) {
            $issues[] = 'Cannot connect to Google OAuth service';
            $this->error('❌ Network connectivity issue');
        } else {
            $this->info('✅ Network connectivity OK');
        }
        
        // Check 7: Server time
        $this->info('5. Checking server time...');
        $serverTime = time();
        $this->info("Server time: " . date('Y-m-d H:i:s', $serverTime));
        
        // Check 8: Try to initialize Firebase
        $this->info('6. Testing Firebase initialization...');
        try {
            // Reset Firebase instance
            FirebaseService::$firebaseMessaging = null;
            
            $factory = new Factory();
            $serviceAccount = FirebaseService::loadServiceAccount();
            $firebase = $factory->withServiceAccount($serviceAccount);
            $messaging = $firebase->createMessaging();
            
            $this->info('✅ Firebase initialization successful');
            
        } catch (\Throwable $e) {
            $issues[] = 'Firebase initialization failed: ' . $e->getMessage();
            $this->error('❌ Firebase initialization failed: ' . $e->getMessage());
        }
        
        // Summary
        $this->newLine();
        $this->info('📋 Diagnosis Summary:');
        
        if (empty($issues)) {
            $this->info('✅ No issues found. Firebase configuration appears to be correct.');
        } else {
            $this->error('❌ Found ' . count($issues) . ' issue(s):');
            foreach ($issues as $issue) {
                $this->line('  • ' . $issue);
            }
            
            $this->newLine();
            $this->info('🔧 Recommended fixes:');
            $this->line('  1. Check if the service account key has expired');
            $this->line('  2. Verify the Firebase project is active');
            $this->line('  3. Ensure server time is synchronized (use NTP)');
            $this->line('  4. Check network connectivity to Google services');
            $this->line('  5. Regenerate service account key if necessary');
            
            if ($this->option('fix')) {
                $this->newLine();
                $this->info('🛠️  Attempting automatic fixes...');
                $this->attemptFixes($issues);
            }
        }
        
        return empty($issues) ? 0 : 1;
    }
    
    private function attemptFixes($issues)
    {
        // Fix 1: Synchronize server time
        $this->info('Attempting to synchronize server time...');
        if (function_exists('exec')) {
            exec('ntpdate -s time.nist.gov 2>&1', $output, $returnCode);
            if ($returnCode === 0) {
                $this->info('✅ Server time synchronized');
            } else {
                $this->warn('⚠️  Could not synchronize server time automatically');
            }
        } else {
            $this->warn('⚠️  exec function not available for time sync');
        }
        
        // Fix 2: Clear any cached Firebase instances
        $this->info('Clearing Firebase cache...');
        FirebaseService::$firebaseMessaging = null;
        $this->info('✅ Firebase cache cleared');
        
        $this->info('Automatic fixes completed. Please test Firebase functionality again.');
    }
} 