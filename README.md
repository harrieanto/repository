**Repository PHP Framework**

`Repository PHP framework` pertama kali di initialisasi pada awal tahun 2016. Karna disebabkan beberapa kendala yang dialami oleh creatornya itu sendiri, maka development framework tersebut terhenti dan baru aktif dikembangkan kembali pada april 2019.

Pada bulan tersebut banyak sekali perombakan yang kita lakukan terutama pada sisi bagaimana framework ini bisa readable dan maintainable sehingga harapannya bisa mendatangkan kontributor aktif yang bisa sama-sama mengembangkan framework ini kelevel yang lebih jauh lagi.

Repository framework ini dibuat karna terinspirasi oleh laravel framework. Karna keingintahuan bagaimana core dari sebuah framework besar dibangun, maka lahirlah repository framework ini.

Mungkin dari sini Anda akan menemukan sejumlah dokumentasi yang tidak lengkap pada framework ini. Harus diakui memang menulis dokumentasi itu jauh sedikit lebih lamban dibandingkan menulis code programnya itu sendiri. Akan tetapi dengan bantuan dari Anda semua kami yakin jika dokumentasi akan cepat diselesaikan secara bertahap.

Anda bisa ikut andil berkontribusi untuk perkembangan framework ini dengan cara ikut serta dalam membangun fitur-fitur yang belum tersedia didalam framework tersebut maupun ikut andil berkontribusi secara non-technical melalui layanan donasi sehingga kami memiliki energi yang cukup untuk mengembangkan framework ini untuk Anda. Salah satu bentuk kontribusi non-technical yang bisa Anda lakukan adalah memiliki buku [The Home Green PHP Aplikasi Framework](https://bandd.web.id) yang sudah kami siapkan untuk Anda selama 8 bulan terkahir ini.

Jika Anda merasa tidak sabar mengetahui bagaimana architecture repository framework ini dibangun, kami sudah menyiapkan sebuah buku khusus yang didalamnya membahas mengenai bagaimana sebuah framework besar dibangun seperti halnya laravel framework. Didalam buku tersebut Anda akan dipertemukan dengan teori sekaligus step by step bagaimana membangun sebuah framework yang bisa Anda kontrol sepenuhnya tanpa suatu batasan atau larangan menerapkan sejumlah `design pattern`.

Selain itu Anda juga akan dipertemukan pada sejumlah materi yang membahas bagaimana menulis code program yang `solid`, `readable`, `refactorable` dan juga `maintainable`. Dengan menerapkan code yang solid maka ketika kita membangun sebuah produk, produk tersebut lebih mudah untuk didevelop tanpa menkonsumsi banyak waktu karna kita tidak perlu melakukan perombakan secara massive ketika suatu business logic berubah maupun berkembang. Anda akan menemukan banyak sekali manfaat yang bisa Anda peroleh ketika mengerti dan memahami bagaimana menulis code program yang solid. Salah satu contoh nyata yang bisa kita ambil dari clean code ini adalah massivenya development dari laravel framework. Banyak developer yang sukarela ikut aktif mengembangkan framework tersebut sehingga framework tersebut bisa menjadi framework php yang kita kenal seperti sekarang ini.

Beberapa fitur pada `Repository PHP Framework` yang bisa kami highlight saat ini adalah:

- Flexible Environment. So you can setup your own structure directory beneath the repository skeleton. Repository will provide you magical power environment like Laravel. The most app workflow you need for working and enjoying with that magical power of this framework is just routing, middleware, controller and provider.
- Authentication: Basic Auth Username/Password, Token API Web Service
- Array Collection
- Event Dispatcher
- Support HTTP Message Middleware PSR-7 Compliance
- Global middleware(You can rolling your own awesome after/before middleware features)
- Support PSR-4 Autoload. Also, you can register your class with `Repository\Component\Bootstrappers\Autoloaders\Autoload` class easily.
- View: Built-in Template Engine like Laravel `BLADE` called `RTENGINE`
- Static Accesor
- Automatic Dependency Resolution
- Container(The heart of repository framework)
- Service Provider: Support Eager/Lazy load
- Cache: Only Support Apc and File. But you can rolling your own
- Environment Manager: .env
- Routing: General, Group, Middleware, Console(next release)
- Routing Handler: Closure and controller based on fully high qualified class name.
- Html Helper
- Validation
- Strategy Configuration
- Debugger(Maybe replaced with third party library like Whoops in another release or you can contribute for more meaningful error handler by seeing the `Repository\Component\Debug\...` and `Repository\Component\Http\Kernel` component)
- Session Handler(We didn't use built-in session cause it's awful)
- Cookie
- Filesystem Abstraction
- Logger based on PSR-3. Only support file logger except you want rolling your own
- Pipeline
- Pagination(Database less. So you can write your own pagination handler with your favorite data source freely without any restrictions. As built-in support array collection and doctrine orm)
- Hashing
- Encryption
- Json Helper
- Mailer
- String Manipulation
- Image Manipulation
- Driver Manager
- Dot Notation
- Console: Only available for output reponse such as table formatter, background, foreground, progressbar and prompted. By means you can't perform any console requst except you define your own request. The console request will provide at next release.


Note. `Repository Framework` only crafted at unix. If you have an issues in another environment. Please tell us.

Next release:

- Console Request
- More flexible validation and view compiler. So you can extending easily
- Support for PSR-15 Middleware
- Window/Ip throttling
- Everything based on interface. So you can change the component freely as long as implementing the same component API interface
- HTTP CURL
- Socket Helper for Realtime Support
- Database? I don't think so. Because I rely on doctrine for persistence tool. But maybe I'll provide it as a seeder helper only as I think the doctrine component is too complicated just for implement database seeder.

Coming soon release as open-source project.

Brought to you for easy and flexible environment for any class who love write software in clean code manner.

Repository will provide you magical power environment like Laravel. The most app workflow you need for working and enjoying with that magical power feature of this framework is just routing, middleware, controller and provider.

You can use different template engine, any persistence toolbox you want to store your data, use any library third-party as your application needs anytime without restriction.


