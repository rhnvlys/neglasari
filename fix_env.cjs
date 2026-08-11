const fs = require('fs');
const { spawnSync } = require('child_process');

let envContent = fs.readFileSync('.env', 'utf-8');
const getEnv = (key) => {
  const match = envContent.match(new RegExp('^' + key + '=(.*)$', 'm'));
  return match ? match[1].replace(/"/g, '') : '';
};

const appKey = getEnv('APP_KEY');

const envs = {
  APP_KEY: appKey,
  APP_ENV: 'production',
  APP_DEBUG: 'true',
  APP_URL: 'https://neglasari-pi.vercel.app',
  LOG_CHANNEL: 'stderr',
  SESSION_DRIVER: 'database',
  CACHE_STORE: 'database',
  QUEUE_CONNECTION: 'sync',
  FILESYSTEM_DISK: 'public'
};

for (const [k, v] of Object.entries(envs)) {
  if (v) {
    console.log('Setting Vercel env:', k);
    spawnSync('npx.cmd', ['vercel', 'env', 'rm', k, 'production', '-y']);
    spawnSync('npx.cmd', ['vercel', 'env', 'add', k, 'production'], { input: v });
  }
}
console.log('Essential Vercel envs updated.');
