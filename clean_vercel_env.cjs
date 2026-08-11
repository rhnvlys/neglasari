const { spawnSync } = require('child_process');

const keysToRemove = [
  'APP_DEBUG', 'APP_ENV', 'APP_KEY', 'APP_URL',
  'DB_CONNECTION', 'DB_DATABASE', 'DB_HOST', 'DB_PASSWORD', 'DB_PORT', 'DB_USERNAME',
  'FILESYSTEM_DISK', 'LOG_CHANNEL', 'QUEUE_CONNECTION', 'SESSION_DRIVER', 'CACHE_STORE'
];

console.log('Removing old Vercel env vars...');
for (const k of keysToRemove) {
  spawnSync('npx.cmd', ['vercel', 'env', 'rm', k, 'production', '-y']);
}

const validEnvs = {
  APP_KEY: 'base64:F1FdnJ57539kVNNfQR7L9+g4eVQMqUMzyjfi4LIbEs8=',
  APP_ENV: 'production',
  APP_DEBUG: 'true',
  APP_URL: 'https://neglasari-pi.vercel.app',
  LOG_CHANNEL: 'stderr',
  SESSION_DRIVER: 'database',
  CACHE_STORE: 'database',
  QUEUE_CONNECTION: 'sync',
  FILESYSTEM_DISK: 'public',
  DB_CONNECTION: 'sqlite',
  DB_DATABASE: '/tmp/database.sqlite'
};

console.log('Adding clean Vercel env vars...');
for (const [k, v] of Object.entries(validEnvs)) {
  const res = spawnSync('npx.cmd', ['vercel', 'env', 'add', k, 'production'], { input: v, encoding: 'utf-8' });
  console.log(`Added ${k}:`, res.status === 0 ? 'OK' : res.stderr);
}
console.log('Done!');
