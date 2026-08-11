const { spawnSync } = require('child_process');

const envs = {
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

for (const [k, v] of Object.entries(envs)) {
  console.log('Cleaning and setting Vercel env:', k, '=>', v);
  spawnSync('npx.cmd', ['vercel', 'env', 'rm', k, 'production', '-y']);
  spawnSync('npx.cmd', ['vercel', 'env', 'add', k, 'production'], { input: Buffer.from(v, 'utf-8') });
}
console.log('All Vercel envs updated cleanly without BOM.');
