# Samsung Smart Lab API - Docker Setup

## Preduvjeti
- Docker
- Docker Compose

## Pokretanje aplikacije

### 1. Build i pokretanje svih servisa
```bash
docker-compose up -d --build
```

### 2. Pokretanje samo aplikacije (ako su baza i redis već pokrenuti)
```bash
docker-compose up app
```

### 3. Pokretanje u development modu
```bash
docker-compose -f docker-compose.yml -f docker-compose.override.yml up
```

## Pristup aplikaciji
- **API**: http://localhost:3500
- **MySQL**: localhost:3308
- **Redis**: localhost:6379

## Konfiguracija

### Environment fajl
Aplikacija koristi `.env.docker` fajl koji je optimizovan za Docker okruženje:
- `DB_HOST=mysql` (umesto 127.0.0.1)
- `REDIS_HOST=redis` (umesto 127.0.0.1)
- `APP_PORT=3500`
- `DB_PORT=3306` (unutar Docker-a)

### Portovi
- **Aplikacija**: 3500 (korišćenjem `php artisan start`)
- **MySQL**: 3308 (na host mašini) → 3306 (u kontejneru)
- **Redis**: 6379

## Korisne komande

### Pogledaj logove
```bash
docker-compose logs -f app
```

### Uđi u kontejner
```bash
docker-compose exec app sh
```

### Pokreni migracije
```bash
docker-compose exec app php artisan migrate
```

### Pokreni seedere
```bash
docker-compose exec app php artisan db:seed
```

### Očisti cache
```bash
docker-compose exec app php artisan config:clear
docker-compose exec app php artisan route:clear
docker-compose exec app php artisan cache:clear
```

### Zaustavi sve servise
```bash
docker-compose down
```

### Zaustavi i obriši volumene
```bash
docker-compose down -v
```

## Troubleshooting

### Problem sa pravima pristupa
```bash
docker-compose exec app chown -R www-data:www-data /var/www/html/storage
docker-compose exec app chmod -R 755 /var/www/html/storage
```

### Problem sa cache-om
```bash
docker-compose exec app php artisan config:clear
docker-compose exec app php artisan route:clear
```

### Restart aplikacije
```bash
docker-compose restart app
```

### Proveri status servisa
```bash
docker-compose ps
```

### Proveri logove baze podataka
```bash
docker-compose logs mysql
```

### Proveri logove Redis-a
```bash
docker-compose logs redis
```

## Plug & Play

Aplikacija je konfigurisana da radi "plug and play":

1. **Pokrenite**: `docker-compose up -d --build`
2. **Pristupite**: http://localhost:3500
3. **Baza podataka**: automatski se kreira sa vašim podacima
4. **Redis**: automatski se pokreće
5. **JWT**: automatski se instalira i konfiguriše

Sve je prilagođeno vašem projektu sa:
- Port 3500 za aplikaciju
- `php artisan start` komanda
- MySQL na portu 3308
- Redis na portu 6379
- JWT autentifikacija
- Tuya API integracija 