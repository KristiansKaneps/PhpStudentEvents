# Studentu pasākumu pārvaldības sistēma

![Logo](public/assets/images/logo-128x128.png)

> **Piezīme:** Projekts vēl ir izstrādes stadijā.

## Projekta apraksts

Studentu pasākumu pārvaldības sistēma ir izstrādāta, lai atvieglotu studentu un pasniedzēju iespēju organizēt,
reģistrēties un piedalīties universitātes pasākumos. Projekts ir veidots, iedvesmojoties no Laravel ietvara, un izmanto
MVC (Model-View-Controller) arhitektūru, kas nodrošina koda modularitāti un vieglu uzturēšanu.

---

## Sistēmas uzstādīšana ar Docker virtualizāciju

### Prasības

1. Sistēmā ir jābūt uzstādītai [Docker](https://www.docker.com/) (https://www.docker.com/) platformai, kas nodrošina
   konteineru virtualizāciju.
2. Operētājsistēmas `hosts` failā ir jāveic resursa kartēšana, pievienojot rindu:
   ```
   127.0.0.1 student-events.local
   ```
    - Windows sistēmā `hosts` fails atrodas:
      ```
      %WinDir%\System32\drivers\etc\hosts
      ```
    - Linux sistēmā `hosts` fails atrodas:
      ```
      /etc/hosts
      ```

### 1. Repozitorija klonēšana

```bash
git clone https://github.com/KristiansKaneps/RTUStudentEvents.git
cd RTUStudentEvents
```

### 2. Docker konteinera palaišana

Nepieciešams uzbūvēt un palaist Docker konteineri (pēc veiksmīgas uzbūvēšanas var izlaist `--build` karogu):

```bash
docker compose up --build
```

### 3. Konfigurācija

Nepieciešams pārkopēt `.env.example` uz `.env`:

```bash
cp .env.example .env
```

> **Piezīme:** Noklusējumā konfigurācija jau ir saderīga ar virtualizēto Docker vidi.

`.env` piemērs:

```dotenv
APP_NAME="Student Events"
ENVIRONMENT=local
#ENVIRONMENT=production
LOCALE=lv,en

DATABASE_HOST=db
DATABASE_NAME=student_events_db
DATABASE_USERNAME=root
DATABASE_PASSWORD=root
```

### 4. Datubāzes migrācijas

Lai sagatavotu datubāzi, nepieciešams palaist migrācijas ar sekojošo komandu:

```bash
php console migrate
```

### 5. Piekļuve sistēmai

Pēc visu darbību veikšanas atveriet tīmekļa pārlūkprogrammu un apmeklējiet:

- **http://student-events.local**  
  (sistēma automātiski pāradresēs uz **https://student-events.local**).

> **Svarīgi:** Pašparakstītais SSL sertifikāts tiks ģenerēts automātiski, taču pārlūkprogramma var brīdināt, ka tas nav
> drošs.

---

## Sistēmas arhitektūra

Projekts izmanto **MVC** arhitektūru:

- **Model**: nodrošina datu pārvaldību un apstrādi;
- **View**: atspoguļo datus lietotāja saskarnē;
- **Controller**: apstrādā lietotāja ievadi un koordinē mijiedarbību starp modeli un skatu.

## Papildu informācija

- Projektā jau ir nodrošināta datubāze (**MariaDB**).
- Projektā jau ir nodrošināts web-serveris (**Apache**).

---

## Neliels apraksts par programmatūras struktūru

Tīmekļa serveris apkalpo pārlūkprogrammu no [`public`](public) direktorijas. Šeit atrodas galvenais
[`index.php`](public/index.php) fails, kas ielādē visu nepieciešamo projekta struktūru, piemēram,
[`autoload.php`](autoload.php) (automātiski ielādē PHP klases), [`config/config.php`](config/config.php)
(ielādē projekta vides konfigurāciju, kas atrodas failā [`.env`](.env)),
[`core/DI/dependency_injection.php`](core/DI/dependency_injection.php) (automātiska "atkarību injicēšana" konstruktoros
vai metodēs/funkcijās, piemēram dažādi servisi, kas definēti direktorijā [`core/Services/`](core/Services)),
[`routes/web.php`](routes/web.php) (sistēmas definētie maršruti, piemēram, sākumlapa `/`, pasākumu lapa `/events`,
kontaktu lapa `/contact` u.t.t.). Sistēmā var tikt izmantoti šādi definēti maršruti, jo pastāv _Apache_ servera
konfigurācijas fails [`.htaccess`](.htaccess), kas visus maršrutus vada caur [`public/index.php`](public/index.php).

Direktorijā [`commands/`](commands) atrodas konsoles komandas, kas palīdz izstrādes gaitā. Viens piemērs tādai
komandai ir komanda `php console migrate`, kas atrodas failā [`MigrateCommand.php`](commands/MigrateCommand.php), kas
migrē datubāzes shēmu uz jaunāku versiju, ja ir izstrādātas jaunas jeb nākamās datubāzes migrācijas, kas atrodas
direktorijā [`database/migrations/`](database/migrations). Šīs migrācijas ir parasti SQL skripti, kuri tiek izpildīti
datubāzes transakcijas ietvaros (tiek izmantota transakcija, lai pārliecinātos, ka datubāze netiek bojāta, ja kādā no
SQL skriptiem ir pieļauta kļūda). Ja ir vēlme izdzēst visas datubāzes tabulas un veikt migrēšanu no jauna, tad šai pašai
komandai var pievienot argumentu: `php console migrate fresh`.

Direktorijā [`localization/`](localization) atrodas lokalizēti teksti/ziņas. Ja lietotāja sesijā ir saglabāta valoda,
tad tā tiek automātiski izmantota skatos, ziņojumos un citur. Ja lietotāja sesijā nav saglabāta valoda, tad tā tiek
automātiski izvēlēta pēc lietotāja tīmekļa pārlūkprogrammas HTTP `Accept-Language` galvenes (header) un sistēmā
definētajām/pieejamajām valodām.

Direktorijā [`views/`](views) atrodas visas sistēmas skatu veidnes. Piemēram:

- [`pages/index.php`](views/pages/index.php): sākumlapas skata veidne;
- [`pages/events/list.php`](views/pages/events/list.php): pasākumu saraksta lapas skata veidne;
- [`pages/contact.php`](views/pages/contact.php): kontaktu lapas skata veidne;
- u.c.

Sistēmas skati var tikt iekļauti izkārtojumos (_layouts_), kuri arī atrodas skatu veidņu direktorijā [`views/`](views).
Piemēram:

- [`layouts/main.php`](views/layouts/main.php): galvenais sistēmas skatu izkārtojums.
- u.c.

Direktorijā [`core/`](core) atrodas visa sistēmas loģika:

```plaintext
.
├── ...
├── core
│   ├── Controllers            # Kontrolieri
│   │   ├── Controller.php     # Bāzes (abstrakta) kontroliera klase
│   │   └── ...
│   ├── Database               # Datubāzes loģika
│   ├── Services               # Servisi
│   │   ├── Service.php        # Bāzes (abstrakta) servisa klase
│   │   └── ...
│   ├── Helper                 # Palīgrīki
│   ├── Localization           # Lokalizācijas loģika
│   ├── Router                 # Maršrutētāja loģika
│   ├── Session                # Lietotāju sesiju loģika
│   └── ...
└── ...
```

Detalizētāk:

- [`Controllers/`](core/Controllers): kontrolieri, kas koordinē sistēmas loģiku un veido skatus ar šādu kontroliera
  sintaksi (piemērs):
  ```php
  class HomeController extends Controller {
      ...
      public function eventList(EventService $eventService): void {
          ...
          $this->render('pages/events/list', ['events' => $eventService->listUpcomingEvents()]);
      }
      ...
  }
  ```
  un šādu veidnes sintaksi (piemērs):
  ```html
  ...
  <section>
    <h2 class="section-title">Gaidāmie pasākumi</h2>
    <ul>
        <?php /** @var array $events - Lai IDE atpazītu PHP mainīgo un tā tipu. */ ?>
        <?php foreach ($events as $event): ?>
            <li>
                <h3><?php echo $event['name']; ?></h3>
                <p><?php echo $event['description']; ?></p>
                <p>Sākas: <?php echo $event['start_date']; ?></p>
            </li>
        <?php endforeach; ?>
    </ul>
  </section>
  ...
  ```
  > **Piezīmes:**
  >   - `EventService` tiek automātiski inicializēts un "injicēts" metodē `eventList`;
  >   - `'pages/events/list'` nozīmē to pašu, ko [`/views/pages/events/list.php`](views/pages/events/list.php).
- [`Database/`](core/Database): datubāzes loģika (savienošanās, vaicājumi u.c.):
    - savienošanās tiek veikta ar PHP PDO, un tiek mainīts savienojuma iestatījums, lai būtu vieglāk izgūt vaicājumu
      rezultātus:
      ```php
      ...
      $this->pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
      ...
      ```
    - datubāzes shēmu pārvalda migrācijas, kas atrodas direktorijā [`/database/migrations/`](database/migrations).
- [`Services/`](core/Services): servisi, kas atbild par sistēmas loģiku, piemēram:
  ```php
  class EventService extends Service {
      ...
      public function listUpcomingEvents(): array {
          return $this->db->executeQuery(<<<SQL
              SELECT e.id, e.name, e.description, e.start_date, e.end_date, 
                     e.current_participant_count, ec.name AS category_name
              FROM events e
              JOIN event_categories ec ON e.category_id = ec.id
              WHERE e.cancelled = false AND e.start_date >= NOW()
              ORDER BY e.start_date ASC, e.end_date DESC
              SQL,
          );
      }
      ...
  }
  ```
- [`Router/`](core/Router): maršrutēšanas loģika, kas ļauj definēt sistēmas maršrutus šādā veidā (piemērs):
  ```php
  Router::get('/', [HomeController::class, 'index'])->name('home', 'index');
  Router::get('/events', [HomeController::class, 'eventList'])->name('events');
  ...
  ```
  Šie maršruti ir definēti failā [`/routes/web.php`](routes/web.php).
- [`Helper/`](core/Helper): palīgfunkcijas/palīgmetodes, kas ļauj:
  - iztulkot statiskus tekstus lietotāja izvēlētajā valodā;
  - vieglāk iegūt sistēmā definētos maršrutus, kad tiek veidoti skati;
  - u.c.
  
  Piemēram, HTML kods:
  ```html
  <a href="/events">Pasākumi</a>
  ```
  var tikt uzrakstīts šādi, lai izmantotu dinamiskos maršrutus un tulkojumus:
  ```php
  <a href="<?= route('events') ?>"><?= t('nav.events') ?></a>
  ```
- u.c.

---

## Potenciālās nākotnes optimizācijas
- [ ] Maršrutu kešošana.

---

## Autori

- [Kristiāns Kaņeps](https://github.com/KristiansKaneps)