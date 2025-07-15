# Firebase Invalid Grant Error Troubleshooting Guide

## Overview
The "invalid_grant" error in Firebase typically occurs when there are authentication issues with the service account credentials or network connectivity problems.

## Common Causes

1. **Expired Service Account Key**
2. **Clock Skew Between Server and Google**
3. **Invalid or Corrupted Service Account JSON**
4. **Network Connectivity Issues**
5. **Firebase Project Configuration Problems**

## Immediate Solutions

### 1. Run the Diagnosis Command
```bash
php artisan firebase:diagnose
```

For automatic fixes:
```bash
php artisan firebase:diagnose --fix
```

### 2. Test Firebase Configuration
Use the test endpoints to verify Firebase functionality:

```bash
# Test configuration loading
curl -X GET "your-domain.com/api/test/firebase-config"

# Test Firebase initialization
curl -X GET "your-domain.com/api/test/firebase-init"

# Test notification sending
curl -X POST "your-domain.com/api/test/firebase-notification" \
  -H "Content-Type: application/json" \
  -d '{"topic": "test-topic", "title": "Test", "body": "Test message"}'
```

### 3. Check Server Time Synchronization
```bash
# Check current server time
date

# Synchronize with NTP server (if available)
sudo ntpdate -s time.nist.gov

# Or use systemd-timesyncd
sudo timedatectl set-ntp true
sudo systemctl restart systemd-timesyncd
```

### 4. Verify Service Account File
```bash
# Check file permissions
ls -la storage/firebase/glint-up-firebase-adminsdk-fbsvc-f7a7261316.json

# Verify JSON structure
cat storage/firebase/glint-up-firebase-adminsdk-fbsvc-f7a7261316.json | jq .

# Check file integrity
md5sum storage/firebase/glint-up-firebase-adminsdk-fbsvc-f7a7261316.json
```

## Advanced Solutions

### 1. Regenerate Service Account Key
1. Go to [Firebase Console](https://console.firebase.google.com/)
2. Select your project
3. Go to Project Settings > Service Accounts
4. Click "Generate new private key"
5. Download the new JSON file
6. Replace the existing file in `storage/firebase/`
7. Update file permissions:
   ```bash
   chmod 600 storage/firebase/glint-up-firebase-adminsdk-fbsvc-f7a7261316.json
   chown www-data:www-data storage/firebase/glint-up-firebase-adminsdk-fbsvc-f7a7261316.json
   ```

### 2. Check Firebase Project Status
1. Verify the Firebase project is active
2. Check if billing is enabled (if required)
3. Ensure Cloud Messaging API is enabled
4. Verify the service account has proper permissions

### 3. Network and Firewall Issues
```bash
# Test connectivity to Google services
curl -I https://oauth2.googleapis.com/token
curl -I https://fcm.googleapis.com/fcm/send

# Check DNS resolution
nslookup oauth2.googleapis.com
nslookup fcm.googleapis.com

# Test with different DNS servers if needed
dig @8.8.8.8 oauth2.googleapis.com
```

### 4. Environment-Specific Issues

#### Production Environment
- Ensure HTTPS is properly configured
- Check if the server can reach Google's services
- Verify no proxy or firewall is blocking requests

#### Development Environment
- Check if you're using the correct service account for the environment
- Ensure the Firebase project matches your environment

## Monitoring and Prevention

### 1. Add Monitoring
The updated FirebaseService now includes:
- Automatic retry logic for invalid_grant errors
- Better error logging with context
- Graceful degradation when Firebase is unavailable

### 2. Regular Maintenance
```bash
# Add to your crontab for regular checks
0 */6 * * * php artisan firebase:diagnose >> /var/log/firebase-health.log 2>&1
```

### 3. Log Monitoring
Monitor these log entries:
```bash
# Check for Firebase errors
tail -f storage/logs/laravel.log | grep "Firebase"

# Check for invalid_grant errors specifically
tail -f storage/logs/laravel.log | grep "invalid_grant"
```

## Emergency Procedures

### If Firebase is Completely Down
1. The notification storage will still work (database notifications)
2. Firebase notifications will be retried automatically
3. Users can still receive notifications through the app's internal notification system

### Rollback Plan
If the issue persists:
1. Temporarily disable Firebase notifications
2. Use alternative notification methods (email, SMS)
3. Implement a fallback notification system

## Contact Information

For persistent issues:
1. Check Firebase Console for project status
2. Review Google Cloud Console for service account issues
3. Contact Firebase Support if needed

## Additional Resources

- [Firebase Admin SDK Documentation](https://firebase.google.com/docs/admin/setup)
- [Google Cloud IAM Documentation](https://cloud.google.com/iam/docs)
- [Firebase Cloud Messaging Documentation](https://firebase.google.com/docs/cloud-messaging) 