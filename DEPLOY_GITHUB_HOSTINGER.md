# Deploy Brands Planets POS With GitHub + Hostinger VPS

## 1. Create GitHub Repo

Create a new GitHub repository, for example:

```bash
brands-planets-pos
```

Then from this project folder:

```bash
git init
git add .
git commit -m "Initial Brands Planets POS"
git branch -M main
git remote add origin https://github.com/YOUR_USERNAME/brands-planets-pos.git
git push -u origin main
```

## 2. Prepare Hostinger VPS

SSH into the VPS:

```bash
ssh root@YOUR_VPS_IP
```

Install Node.js, Git, Nginx, and PM2:

```bash
curl -fsSL https://deb.nodesource.com/setup_20.x | sudo -E bash -
sudo apt install -y nodejs git nginx
sudo npm install -g pm2
```

Create the app folder:

```bash
sudo mkdir -p /var/www/brands-planets-pos
sudo chown -R $USER:$USER /var/www/brands-planets-pos
```

## 3. Add GitHub Secrets

In GitHub repo:

`Settings > Secrets and variables > Actions > New repository secret`

Add:

```text
HOSTINGER_HOST      your VPS IP or pos-host.brandsplanets.online
HOSTINGER_USER      root or your VPS username
HOSTINGER_PORT      22
HOSTINGER_SSH_KEY   your private SSH key
REPO_URL            git@github.com:YOUR_USERNAME/brands-planets-pos.git
```

## 4. Connect Domain With Nginx

On VPS:

```bash
sudo nano /etc/nginx/sites-available/brands-planets-pos
```

Paste:

```nginx
server {
    server_name pos-host.brandsplanets.online;

    location / {
        proxy_pass http://127.0.0.1:3000;
        proxy_http_version 1.1;
        proxy_set_header Host $host;
        proxy_set_header X-Real-IP $remote_addr;
        proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
        proxy_set_header X-Forwarded-Proto $scheme;
    }
}
```

Enable it:

```bash
sudo ln -s /etc/nginx/sites-available/brands-planets-pos /etc/nginx/sites-enabled/
sudo nginx -t
sudo systemctl reload nginx
```

## 5. Add SSL

```bash
sudo apt install -y certbot python3-certbot-nginx
sudo certbot --nginx -d pos-host.brandsplanets.online
```

## 6. Deploy

Push to GitHub:

```bash
git add .
git commit -m "Deploy POS"
git push
```

GitHub Actions will SSH into Hostinger, pull the latest code, install dependencies, and restart PM2.
