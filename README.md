<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## About Laravel

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel takes the pain out of development by easing common tasks used in many web projects, such as:

- [Simple, fast routing engine](https://laravel.com/docs/routing).
- [Powerful dependency injection container](https://laravel.com/docs/container).
- Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
- Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
- Database agnostic [schema migrations](https://laravel.com/docs/migrations).
- [Robust background job processing](https://laravel.com/docs/queues).
- [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

Laravel is accessible, powerful, and provides tools required for large, robust applications.

## Learning Laravel

Laravel has the most extensive and thorough [documentation](https://laravel.com/docs) and video tutorial library of all modern web application frameworks, making it a breeze to get started with the framework. You can also check out [Laravel Learn](https://laravel.com/learn), where you will be guided through building a modern Laravel application.

If you don't feel like reading, [Laracasts](https://laracasts.com) can help. Laracasts contains thousands of video tutorials on a range of topics including Laravel, modern PHP, unit testing, and JavaScript. Boost your skills by digging into our comprehensive video library.

## Laravel Sponsors

We would like to extend our thanks to the following sponsors for funding Laravel development. If you are interested in becoming a sponsor, please visit the [Laravel Partners program](https://partners.laravel.com).

### Premium Partners

- **[Vehikl](https://vehikl.com)**
- **[Tighten Co.](https://tighten.co)**
- **[Kirschbaum Development Group](https://kirschbaumdevelopment.com)**
- **[64 Robots](https://64robots.com)**
- **[Curotec](https://www.curotec.com/services/technologies/laravel)**
- **[DevSquad](https://devsquad.com/hire-laravel-developers)**
- **[Redberry](https://redberry.international/laravel-development)**
- **[Active Logic](https://activelogic.com)**

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

```
drivease
├─ .editorconfig
├─ app
│  ├─ Events
│  │  └─ PesanTerkirim.php
│  ├─ Http
│  │  ├─ Controllers
│  │  │  ├─ Admin
│  │  │  │  ├─ AkuntansiController.php
│  │  │  │  ├─ ChatController.php
│  │  │  │  ├─ DashboardController.php
│  │  │  │  ├─ LaporanController.php
│  │  │  │  ├─ MobilController.php
│  │  │  │  ├─ NotifikasiController.php
│  │  │  │  ├─ PemesananController.php
│  │  │  │  └─ UserController.php
│  │  │  ├─ Auth
│  │  │  │  ├─ AuthenticatedSessionController.php
│  │  │  │  ├─ ConfirmablePasswordController.php
│  │  │  │  ├─ EmailVerificationNotificationController.php
│  │  │  │  ├─ EmailVerificationPromptController.php
│  │  │  │  ├─ GoogleController.php
│  │  │  │  ├─ NewPasswordController.php
│  │  │  │  ├─ PasswordController.php
│  │  │  │  ├─ PasswordResetLinkController.php
│  │  │  │  ├─ RegisteredUserController.php
│  │  │  │  └─ VerifyEmailController.php
│  │  │  ├─ Controller.php
│  │  │  ├─ ProfileController.php
│  │  │  └─ User
│  │  │     ├─ ChatController.php
│  │  │     ├─ DashboardController.php
│  │  │     ├─ FavoritController.php
│  │  │     ├─ MobilController.php
│  │  │     ├─ NotifikasiController.php
│  │  │     ├─ PaymentController.php
│  │  │     ├─ PemesananController.php
│  │  │     └─ ProfilController.php
│  │  ├─ Middleware
│  │  │  ├─ EnsureEmailVerified.php
│  │  │  └─ IsAdmin.php
│  │  └─ Requests
│  │     ├─ Auth
│  │     │  └─ LoginRequest.php
│  │     └─ ProfileUpdateRequest.php
│  ├─ Jobs
│  │  ├─ KirimEmailPemesanan.php
│  │  └─ KirimPengingatSewa.php
│  ├─ Mail
│  │  ├─ PemesananDibatalkan.php
│  │  ├─ PemesananDibayar.php
│  │  ├─ PemesananDibuat.php
│  │  ├─ PemesananDikonfirmasi.php
│  │  ├─ PemesananDitolak.php
│  │  ├─ PemesananSelesai.php
│  │  ├─ PengingatSewa.php
│  │  └─ PesananBaruAdmin.php
│  ├─ Models
│  │  ├─ Account.php
│  │  ├─ Favorit.php
│  │  ├─ JournalEntry.php
│  │  ├─ Mobil.php
│  │  ├─ Notifikasi.php
│  │  ├─ Page.php
│  │  ├─ Payment.php
│  │  ├─ Pemesanan.php
│  │  ├─ Pesan.php
│  │  └─ User.php
│  ├─ Providers
│  │  └─ AppServiceProvider.php
│  └─ View
│     └─ Components
│        ├─ AppLayout.php
│        └─ GuestLayout.php
├─ artisan
├─ bootstrap
│  ├─ app.php
│  ├─ cache
│  │  ├─ packages.php
│  │  └─ services.php
│  └─ providers.php
├─ composer.json
├─ composer.lock
├─ config
│  ├─ app.php
│  ├─ auth.php
│  ├─ broadcasting.php
│  ├─ cache.php
│  ├─ database.php
│  ├─ filesystems.php
│  ├─ logging.php
│  ├─ mail.php
│  ├─ midtrans.php
│  ├─ queue.php
│  ├─ reverb.php
│  ├─ services.php
│  └─ session.php
├─ database
│  ├─ database.sqlite
│  ├─ factories
│  │  └─ UserFactory.php
│  ├─ migrations
│  │  ├─ 0001_01_01_000000_create_users_table.php
│  │  ├─ 0001_01_01_000001_create_cache_table.php
│  │  ├─ 0001_01_01_000002_create_jobs_table.php
│  │  ├─ 2026_06_06_090445_add_fields_to_users_table.php
│  │  ├─ 2026_06_06_090458_create_mobils_table.php
│  │  ├─ 2026_06_06_090507_create_pemesanans_table.php
│  │  ├─ 2026_06_06_090517_create_payments_table.php
│  │  ├─ 2026_06_06_090526_create_accounts_table.php
│  │  ├─ 2026_06_06_090533_create_journal_entries_table.php
│  │  ├─ 2026_06_06_090539_create_notifikasis_table.php
│  │  ├─ 2026_06_06_090545_create_favorits_table.php
│  │  ├─ 2026_06_06_090553_create_pesans_table.php
│  │  └─ 2026_06_08_195238_create_pages_table.php
│  └─ seeders
│     ├─ AccountSeeder.php
│     ├─ DatabaseSeeder.php
│     └─ UserSeeder.php
├─ lang
│  ├─ en
│  │  ├─ auth.php
│  │  ├─ pagination.php
│  │  ├─ passwords.php
│  │  └─ validation.php
│  ├─ id
│  │  ├─ actions.php
│  │  ├─ auth.php
│  │  ├─ http-statuses.php
│  │  ├─ pagination.php
│  │  ├─ passwords.php
│  │  └─ validation.php
│  └─ id.json
├─ package-lock.json
├─ package.json
├─ phpunit.xml
├─ postcss.config.js
├─ public
│  ├─ .htaccess
│  ├─ favicon.ico
│  ├─ index.php
│  └─ robots.txt
├─ README.md
├─ resources
│  ├─ css
│  │  └─ app.css
│  ├─ js
│  │  ├─ app.js
│  │  └─ bootstrap.js
│  └─ views
│     ├─ admin
│     │  ├─ akuntansi
│     │  │  ├─ index.blade.php
│     │  │  └─ jurnal.blade.php
│     │  ├─ chat
│     │  │  └─ index.blade.php
│     │  ├─ dashboard.blade.php
│     │  ├─ laporan
│     │  │  └─ index.blade.php
│     │  ├─ mobil
│     │  │  ├─ create.blade.php
│     │  │  ├─ edit.blade.php
│     │  │  └─ index.blade.php
│     │  ├─ notifikasi
│     │  │  └─ index.blade.php
│     │  ├─ pemesanan
│     │  │  ├─ index.blade.php
│     │  │  └─ show.blade.php
│     │  └─ user
│     │     └─ index.blade.php
│     ├─ auth
│     │  ├─ confirm-password.blade.php
│     │  ├─ forgot-password.blade.php
│     │  ├─ login.blade.php
│     │  ├─ register.blade.php
│     │  ├─ reset-password.blade.php
│     │  └─ verify-email.blade.php
│     ├─ components
│     │  ├─ admin-drawer.blade.php
│     │  ├─ admin-nav.blade.php
│     │  ├─ alert.blade.php
│     │  ├─ application-logo.blade.php
│     │  ├─ auth-modal.blade.php
│     │  ├─ auth-session-status.blade.php
│     │  ├─ avatar.blade.php
│     │  ├─ badge.blade.php
│     │  ├─ button.blade.php
│     │  ├─ card.blade.php
│     │  ├─ danger-button.blade.php
│     │  ├─ dropdown-link.blade.php
│     │  ├─ dropdown.blade.php
│     │  ├─ empty-state.blade.php
│     │  ├─ icon.blade.php
│     │  ├─ input-error.blade.php
│     │  ├─ input-label.blade.php
│     │  ├─ input.blade.php
│     │  ├─ modal.blade.php
│     │  ├─ nav-link.blade.php
│     │  ├─ page-header.blade.php
│     │  ├─ primary-button.blade.php
│     │  ├─ responsive-nav-link.blade.php
│     │  ├─ secondary-button.blade.php
│     │  ├─ select.blade.php
│     │  ├─ stat-card.blade.php
│     │  ├─ status-badge.blade.php
│     │  ├─ text-input.blade.php
│     │  └─ textarea.blade.php
│     ├─ emails
│     │  ├─ layout.blade.php
│     │  ├─ pemesanan-dibatalkan.blade.php
│     │  ├─ pemesanan-dibayar.blade.php
│     │  ├─ pemesanan-dibuat.blade.php
│     │  ├─ pemesanan-dikonfirmasi.blade.php
│     │  ├─ pemesanan-ditolak.blade.php
│     │  ├─ pemesanan-selesai.blade.php
│     │  ├─ pengingat-sewa.blade.php
│     │  └─ pesanan-baru-admin.blade.php
│     ├─ layouts
│     │  ├─ admin.blade.php
│     │  ├─ app.blade.php
│     │  ├─ guest.blade.php
│     │  └─ navigation.blade.php
│     ├─ pages
│     │  ├─ privacy.blade.php
│     │  └─ terms.blade.php
│     ├─ profile
│     │  ├─ edit.blade.php
│     │  └─ partials
│     │     ├─ delete-user-form.blade.php
│     │     ├─ update-password-form.blade.php
│     │     └─ update-profile-information-form.blade.php
│     ├─ user
│     │  ├─ chat
│     │  │  └─ index.blade.php
│     │  ├─ dashboard.blade.php
│     │  ├─ favorit
│     │  │  └─ index.blade.php
│     │  ├─ mobil
│     │  │  ├─ index.blade.php
│     │  │  └─ show.blade.php
│     │  ├─ notifikasi
│     │  │  └─ index.blade.php
│     │  ├─ payment
│     │  │  └─ checkout.blade.php
│     │  ├─ pemesanan
│     │  │  ├─ create.blade.php
│     │  │  └─ index.blade.php
│     │  └─ profil
│     │     └─ edit.blade.php
│     └─ welcome.blade.php
├─ routes
│  ├─ auth.php
│  ├─ channels.php
│  ├─ console.php
│  └─ web.php
├─ storage
│  ├─ app
│  │  ├─ private
│  │  └─ public
│  │     └─ mobil
│  │        ├─ 1tn5MddYLFebAJ60LCLAPXKuin8ckmpDw2janxVK.jpg
│  │        ├─ atP4UAXro6Mf14NQRLlQcG6bWLU8yTC6oV4ldaoy.jpg
│  │        ├─ mhjd4RBq3ai9L8s7AgjNM9qJhcJq23rPhubD62Wr.jpg
│  │        ├─ pyEowAHyuiunGZKlkVMHSQQXC1kmbU4cuUts1ikr.jpg
│  │        ├─ RVyofl48Aarts5izD1uaHgTHZmuYVQz5uhsaO6ax.jpg
│  │        ├─ thAQDMR9cOPfoPEDTg3tPwCNA6acZUzEKy9RBADs.jpg
│  │        └─ xKydqFV2pPzYZdfbwlFR78X4hsJqUmz3TnFq3auf.jpg
│  ├─ framework
│  │  ├─ cache
│  │  │  └─ data
│  │  ├─ sessions
│  │  ├─ testing
│  │  └─ views
│  │     ├─ 01d77fb40cf880ff2ab109a12e9bb11a.php
│  │     ├─ 08eb33a48df154c8f54f7eb6eb08dcb7.php
│  │     ├─ 0a1c840e9a6929a5bf5168430f4742c5.php
│  │     ├─ 0c1a6bfb2439d4dac131dee37876f0e7.php
│  │     ├─ 0e86586d9ea0079ad1729bbb73c9aa8a.php
│  │     ├─ 133b52458b300e1b46e9475ec76a2832.php
│  │     ├─ 1b13edc49263b0733d3d0da9669aa1a5.php
│  │     ├─ 1bc0a43d8dad6cd46fd0b7d3935fbeb8.php
│  │     ├─ 1fdf1c9fdcdb9ab679340af5cee7887f.php
│  │     ├─ 213dd0980ce964c6811f484063e9fa85.php
│  │     ├─ 27bf6fbb9e056d07825ce2f738d77fe6.php
│  │     ├─ 2a1e50f98f590f86d53eb499f0f25728.php
│  │     ├─ 328a6e5c300624cd044c4dd16cdf00ce.php
│  │     ├─ 357beb4ad5d01e0a2b4fd780c652534d.php
│  │     ├─ 3c7a4b82eabd939e0b0fe246e4b23505.php
│  │     ├─ 40bcbd013e883dae93255779d4fb1470.php
│  │     ├─ 47e0ae44f038f7705aa5ad6830b7d10a.php
│  │     ├─ 49fe0cd034c74d929d59da696d1d262c.php
│  │     ├─ 4b2cee1ea2b34b1ab0ea576ff509ebed.php
│  │     ├─ 4b4e1eb0e02bebf616d9acb83bdac53e.php
│  │     ├─ 52072649d95b3e11e572cbdf22079619.php
│  │     ├─ 52fbaa844546218b2cc4aca52185dd7c.php
│  │     ├─ 54fed5b26df51acb7b635bf11f11fc3c.php
│  │     ├─ 55ac3ddab9c5107830da1b37c8121802.php
│  │     ├─ 5922d5df75558350ef5faeb6a6bc92f2.php
│  │     ├─ 5b5bc1c56594aef3a0f4855d1e03a331.php
│  │     ├─ 607de5d465fbbebdad8dcb8a69713bc4.php
│  │     ├─ 612b38bb3380a4a073cc38d87eca176f.php
│  │     ├─ 626a59ec43ecdb829aa10f9ed3bd7c59.php
│  │     ├─ 667369ff47782554ae3c70a5dc297836.php
│  │     ├─ 694c74c18bab52b85059521d44c2f537.php
│  │     ├─ 6cb8c0bafd28ba974cc5acda6a275b63.php
│  │     ├─ 6e94f5f7247e073345689246b8efc8c8.php
│  │     ├─ 6efad22027a4a0fbad9f5cd49abe5afb.php
│  │     ├─ 7060ec97405a155d2b0fc622ed2dbf62.php
│  │     ├─ 712a1fc06cac14c72ffcf66418bfd417.php
│  │     ├─ 743c8996c0ac71c96e17c325dbddc42e.php
│  │     ├─ 7776a381f5c671f0280e79ebdac2aa4c.php
│  │     ├─ 791f00213c835e03e5670448bcf84df6.php
│  │     ├─ 79896fe7b17d591fdcc65ee6c456e1e6.php
│  │     ├─ 7b137011622ed066c97e5d8202346a53.php
│  │     ├─ 7c42d7121483f049758ad15e8237ad94.php
│  │     ├─ 7fe62767a2f763402bd4ded62b5352a7.php
│  │     ├─ 80a687c587b276534cb481e5cdbcb8e6.php
│  │     ├─ 81fe98bb8c3e89b983c907bf8cf4b616.php
│  │     ├─ 8711b7729f03e3f01ba88a8b82b0752c.php
│  │     ├─ 87da92eb2793b3ec7fe6131c53966265.php
│  │     ├─ 8aeab29fac6b584fb617bc03d91d4a50.php
│  │     ├─ 8c6b1039ca4751841564e802c34e62c8.php
│  │     ├─ 9150217f15f85966266101bfdd80475a.php
│  │     ├─ 91527887eda521a4e74c32e637b142c2.php
│  │     ├─ 919c41e99172157315e372e20f432b7f.php
│  │     ├─ 98793b3fdee5a5bd0875ec71db9f883a.php
│  │     ├─ 99f47f3a45b5b76d4afd037424ada564.php
│  │     ├─ 9cc5bc0a411f2b680b183b43e868d983.php
│  │     ├─ 9e79a1bd9e7eea546f0cccf53baaa97a.php
│  │     ├─ 9f6911ffb6ff1f658e277a91bddff9d4.php
│  │     ├─ a367e6230fd300c6db108493a39b6108.php
│  │     ├─ ab544fb42c5ff720335563de946c05bd.php
│  │     ├─ af52d334cf4884b375c25f66fcc5512f.php
│  │     ├─ b1f93ef22d83f5e4d77a74adf1df7175.php
│  │     ├─ c10b202b81adfcd1907788bd731653d0.php
│  │     ├─ c4402b17afd07bc3bfde2c913f7522f8.php
│  │     ├─ c7735c2ebccea1c685ded7fdd9d12ca5.php
│  │     ├─ c867d1a8fc3da2bfd99c2e6c87abaf14.php
│  │     ├─ c93642f51eaf3759749929dadf34c922.php
│  │     ├─ cacd79cfbea491d406f766beaa0699c7.php
│  │     ├─ dadc17caeb5f432ae0760e947b5e403e.php
│  │     ├─ dbb037970b241310c5a60f30806e0ccc.php
│  │     ├─ df619c8c16394c6541705c8565e0d310.php
│  │     ├─ e4dc177da9326b1b8f9888046b975843.php
│  │     ├─ e6a13c21578e82c26081dbc29126b217.php
│  │     ├─ e928cbbf52ef68a7c567ca24bde0176c.php
│  │     ├─ edab90e8dfa0e234344efbb0e873ee5f.php
│  │     ├─ ee0297f31df5fc6dfde482f15d0ee5d1.php
│  │     └─ fb536733a426f31001ce9149fc238e4e.php
│  └─ logs
├─ tailwind.config.js
├─ tests
│  ├─ Feature
│  │  ├─ Auth
│  │  │  ├─ AuthenticationTest.php
│  │  │  ├─ EmailVerificationTest.php
│  │  │  ├─ PasswordConfirmationTest.php
│  │  │  ├─ PasswordResetTest.php
│  │  │  ├─ PasswordUpdateTest.php
│  │  │  └─ RegistrationTest.php
│  │  ├─ ExampleTest.php
│  │  └─ ProfileTest.php
│  ├─ TestCase.php
│  └─ Unit
│     └─ ExampleTest.php
└─ vite.config.js

```