<?php
session_start();
require_once 'config/database.php';

// Placeholder for generateCSRFToken function if not defined elsewhere
if (!function_exists('generateCSRFToken')) {
    function generateCSRFToken() {
        // This is a placeholder. A proper CSRF token implementation is needed.
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }
}

// Get all products with prepared statement
$stmt = $pdo->prepare("SELECT * FROM products ORDER BY created_at DESC");
$stmt->execute();
$products = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <title>BanyumaSportHub - Your Sports Equipment Marketplace</title>
  <meta name="description" content="">
  <meta name="keywords" content="">

  <!-- Favicons -->
  <link href="assets/img/favicon.png" rel="icon">
  <link href="assets/img/apple-touch-icon.png" rel="apple-touch-icon">

  <!-- Fonts -->
  <link href="https://fonts.googleapis.com" rel="preconnect">
  <link href="https://fonts.gstatic.com" rel="preconnect" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&family=Open+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;1,300;1,400;1,500;1,600;1,700;1,800&family=Montserrat:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">

  <!-- Vendor CSS Files -->
  <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
  <link href="assets/vendor/aos/aos.css" rel="stylesheet">
  <link href="assets/vendor/glightbox/css/glightbox.min.css" rel="stylesheet">
  <link href="assets/vendor/swiper/swiper-bundle.min.css" rel="stylesheet">

  <!-- Main CSS File -->
  <link href="assets/css/main.css" rel="stylesheet">
</head>

<body class="index-page">

  <header id="header" class="header fixed-top">
    <div class="container d-flex align-items-center justify-content-between">
      <a href="index.php" class="logo d-flex align-items-center">
        <h1 class="sitename">SportHub</h1>
      </a>

      <nav id="navmenu" class="navmenu">
        <ul>
          <li><a href="#hero" class="active">Home</a></li>
          <li><a href="#about">About</a></li>
          <li><a href="#testimonials">Event</a></li>
          <li><a href="#lapangan">Lapangan</a></li>
          <li><a href="#komunitas">Komunitas</a></li>
          <li><a href="#marketplace">Marketplace</a></li>
          <li><a href="#artikel">Artikel</a></li>
          <li class="ms-3">
            <a href="cart.php" class="d-flex align-items-center position-relative">
              <i class="bi bi-cart"></i>
              <?php if (isset($_SESSION['cart']) && count($_SESSION['cart']) > 0): ?>
                <span class="badge bg-primary ms-1 position-absolute" style="top: -8px; right: -8px;"><?php echo count($_SESSION['cart']); ?></span>
              <?php endif; ?>
            </a>
          </li>
          <?php if (isset($_SESSION['user_id']) && !empty($_SESSION['username'])): ?>
            <li class="ms-3 dropdown">
              <a href="#" class="btn btn-link dropdown-toggle d-flex align-items-center text-decoration-none p-0" id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                <i class="bi bi-person-circle me-2"></i>
                <span class="d-none d-md-inline"><?php echo htmlspecialchars($_SESSION['username']); ?></span>
              </a>
              <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                <li>
                  <a class="dropdown-item d-flex align-items-center text-danger" href="logout.php">
                    <i class="bi bi-box-arrow-right me-2"></i>
                    <span>Logout</span>
                  </a>
                </li>
              </ul>
            </li>
          <?php else: ?>
            <li class="ms-3">
              <a href="login.php" class="d-flex align-items-center">
                <i class="bi bi-person-circle me-2"></i>
                <span class="d-none d-md-inline">Login</span>
              </a>
            </li>
          <?php endif; ?>
        </ul>
        <i class="mobile-nav-toggle d-xl-none bi bi-list"></i>
      </nav>
    </div>
  </header>

  <main class="main">

    <!-- Hero Section -->
    <section id="hero" class="hero section light-background">

      <div class="container">
        <div class="row gy-4">
          <div class="col-lg-6 order-2 order-lg-1 d-flex flex-column justify-content-center" data-aos="zoom-out">
            <h1>Welcome to BanyumaSportHub</h1>
            <p>Your one-stop shop for all sports equipment and accessories.</p>
            <div class="d-flex">
              <a href="#marketplace" class="btn-get-started">Shop Now</a>
            </div>
          </div>
        </div>
      </div>

    </section><!-- /Hero Section -->

    <!-- Marketplace Section -->
    <section id="marketplace" class="marketplace section light-background">

      <!-- Section Title -->
      <div class="container section-title" data-aos="fade-up">
        <div class="section-badge">MARKETPLACE</div>
        <h2 class="section-main-title text-primary fw-bold">Check Our Marketplace</h2>
      </div><!-- End Section Title -->

      <div class="container">
        <div class="row g-4">
            <?php
            $stmt = $pdo->query("SELECT * FROM products ORDER BY created_at DESC");
            while ($product = $stmt->fetch()) {
            ?>
            <div class="col-lg-3 col-md-4 col-sm-6">
                <div class="product-card h-100">
                    <div class="product-image">
                        <a href="#" class="product-preview-trigger" 
                           data-bs-toggle="modal" 
                           data-bs-target="#productPreviewModal"
                           data-id="<?php echo $product['id']; ?>"
                           data-name="<?php echo htmlspecialchars($product['name']); ?>"
                           data-price="<?php echo number_format($product['price'], 0, ',', '.'); ?>"
                           data-image="<?php echo htmlspecialchars($product['image_url']); ?>"
                           data-stock="<?php echo $product['stock']; ?>"
                           data-description="<?php echo htmlspecialchars($product['description'] ?? ''); ?>">
                          <img src="<?php echo htmlspecialchars($product['image_url']); ?>" 
                               alt="<?php echo htmlspecialchars($product['name']); ?>" 
                               class="img-fluid">
                        </a>
                        <?php if ($product['stock'] <= 5 && $product['stock'] > 0): ?>
                            <div class="stock-badge">Hampir Habis</div>
                        <?php endif; ?>
                    </div>
                    <div class="product-info">
                        <h3 class="product-name">
                            <a href="#" class="product-preview-trigger"
                               data-bs-toggle="modal" 
                               data-bs-target="#productPreviewModal"
                               data-id="<?php echo $product['id']; ?>"
                               data-name="<?php echo htmlspecialchars($product['name']); ?>"
                               data-price="<?php echo number_format($product['price'], 0, ',', '.'); ?>"
                               data-image="<?php echo htmlspecialchars($product['image_url']); ?>"
                               data-stock="<?php echo $product['stock']; ?>"
                               data-description="<?php echo htmlspecialchars($product['description'] ?? ''); ?>">
                                <?php echo htmlspecialchars($product['name']); ?>
                            </a>
                        </h3>
                        <div class="product-price">
                            Rp <?php echo number_format($product['price'], 0, ',', '.'); ?>
                        </div>
                        <div class="product-meta">
                            <span class="stock">Stok: <?php echo $product['stock']; ?></span>
                            <span class="location">Jakarta</span>
                        </div>
                        <?php if (isset($_SESSION['user_id'])): ?>
                            <div class="d-flex gap-2">
                                <?php if ($product['stock'] > 0): ?>
                                    <form action="cart.php" method="POST" class="flex-grow-1">
                                        <input type="hidden" name="action" value="add">
                                        <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                                        <input type="hidden" name="quantity" value="1">
                                        <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                                        <button type="submit" class="btn btn-outline-primary w-100">
                                            <i class="bi bi-cart-plus me-1"></i>Add to Cart
                                        </button>
                                    </form>
                                    
                                    <?php if (isset($_SESSION['cart']) && is_array($_SESSION['cart']) && count($_SESSION['cart']) > 0): ?>
                                        <a href="cart.php" class="btn btn-primary flex-grow-1">
                                            <i class="bi bi-cart me-1"></i>View Cart
                                        </a>
                                    <?php else: ?>
                                        <a href="checkout.php?product_id=<?php echo $product['id']; ?>&quantity=1&csrf_token=<?php echo generateCSRFToken(); ?>" class="btn btn-primary flex-grow-1">
                                            <i class="bi bi-credit-card me-1"></i>Checkout
                                        </a>
                                    <?php endif; ?>
                                <?php else: ?>
                                    <button class="btn btn-secondary w-100" disabled>
                                        <i class="bi bi-x-circle me-1"></i>Stok Habis
                                    </button>
                                <?php endif; ?>
                            </div>
                        <?php else: ?>
                            <?php if ($product['stock'] > 0): ?>
                                <a href="login.php" class="btn btn-primary w-100">
                                    <i class="bi bi-box-arrow-in-right me-1"></i>Login to Purchase
                                </a>
                            <?php else: ?>
                                <button class="btn btn-secondary w-100" disabled>
                                    <i class="bi bi-x-circle me-1"></i>Stok Habis
                                </button>
                            <?php endif; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <?php } ?>
        </div>
      </div>
    </section>

    <!-- About Section -->
    <section id="about" class="about section light-background">

      <!-- Section Title -->
      <div class="container section-title" data-aos="fade-up">
        <div class="section-badge">ABOUT</div>
        <h2 class="section-main-title text-primary fw-bold">Apa Itu Banyumas SportHub?!</h2>
      </div><!-- End Section Title -->

      <div class="container">
        <div class="row gy-3">

          <div class="col-lg-6" data-aos="fade-up" data-aos-delay="100">
            <img src="assets/img/gambar.jpg" alt="" class="img-fluid">
          </div>

          <div class="col-lg-6 d-flex flex-column justify-content-center" data-aos="fade-up" data-aos-delay="200">
            <div class="about-content ps-0 ps-lg-3">
                <h3 class="text-center">
                  Banyumas Sport Hub merupakan platform digital yang hadir sebagai pusat informasi, inspirasi, dan kolaborasi di bidang olahraga untuk masyarakat Kabupaten Banyumas.untuk Banyumas Sport Hub mewadahi berbagai kegiatan olahraga lokal, mulai dari berita dan artikel informatif, jadwal event olahraga, hingga informasi fasilitas dan komunitas olahraga yang ada di Banyumas. </h3>
                <p class="fst-italic text-justify">
                </p>
            </div>
        </div>        
      </div>

    </section><!-- /About Section -->

        <!-- Event Section -->
         
        <section id="testimonials" class="testimonials section dark-background">

          <img src="assets/img/event - bg.png" class="testimonials-bg" alt="">
    
          <div class="container" data-aos="fade-up" data-aos-delay="100">
    
            <div class="swiper init-swiper">
              <script type="application/json" class="swiper-config">
                {
                  "loop": true,
                  "speed": 600,
                  "autoplay": {
                    "delay": 5000
                  },
                  "slidesPerView": "auto",
                  "pagination": {
                    "el": ".swiper-pagination",
                    "type": "bullets",
                    "clickable": true
                  }
                }
              </script>
              <div class="swiper-wrapper">
    
                <div class="swiper-slide">
                  <div class="testimonial-item">
                    <img src="assets/img/testimonials/event-1.jpeg" class="testimonial-img" alt="">
                    <h3>Kejuaraan Bulutangkis</h3>
                    <h4>11-14 Mei 2025</h4>
                    <p>
                      <i class="bi bi-quote quote-icon-left"></i>
                      <span>Gedung Sebaguna, GOR Satria Purwokerto</span>
                      <i class="bi bi-quote quote-icon-right"></i>
                    </p>
                  </div>
                </div><!-- End testimonial item -->
    
                <div class="swiper-slide">
                  <div class="testimonial-item">
                    <img src="assets/img/testimonials/event-2.jpeg" class="testimonial-img" alt="">
                    <h3>Kejuaraan Pencak Silat</h3>
                    <h4>11-14 Mei 2025</h4>
                    <p>
                      <i class="bi bi-quote quote-icon-left"></i>
                      <span>Gedung Sasana Krida, GOR Satria Purwokerto</span>
                      <i class="bi bi-quote quote-icon-right"></i>
                    </p>
                  </div>
                </div><!-- End testimonial item -->
    
                <div class="swiper-slide">
                  <div class="testimonial-item">
                    <img src="assets/img/testimonials/event-3.jpeg" class="testimonial-img" alt="">
                    <h3>Turnamen Futsal</h3>
                    <h4> 22-23 Februari 2025</h4>
                    <p>
                      <i class="bi bi-quote quote-icon-left"></i>
                      <span>Gedung Sasana Krida, GOR Satria Purwokerto</span>
                      <i class="bi bi-quote quote-icon-right"></i>
                    </p>
                  </div>
                </div><!-- End testimonial item -->
    
                <div class="swiper-slide">
                  <div class="testimonial-item">
                    <img src="assets/img/testimonials/event-4.jpeg" class="testimonial-img" alt="">
                    <h3>BGS Beach Run</h3>
                    <h4> 1 Juni 2025</h4>
                    <p>
                      <i class="bi bi-quote quote-icon-left"></i>
                      <span>Pantai Kemiren Cilacap</span>
                      <i class="bi bi-quote quote-icon-right"></i>
                    </p>
                  </div>
                </div><!-- End testimonial item -->
    
                <div class="swiper-slide">
                  <div class="testimonial-item">
                    <img src="assets/img/testimonials/event-4.jpeg" class="testimonial-img" alt="">
                    <h3>BGS Beach Run</h3>
                    <h4> 1 Juni 2025</h4>
                    <p>
                      <i class="bi bi-quote quote-icon-left"></i>
                      <span>Pantai Kemiren Cilacap</span>
                      <i class="bi bi-quote quote-icon-right"></i>
                    </p>
                  </div>
                </div><!-- End testimonial item -->
    
              </div>
              <div class="swiper-pagination"></div>
            </div>
    
          </div>
    
        </section><!-- /Testimonials Section -->

    <!-- Lapangan Section -->
    <section id="lapangan" class="lapangan section">

      <!-- Section Title -->
      <div class="container section-title" data-aos="fade-up">
        <div class="section-badge">LAPANGAN</div>
        <h2 class="section-main-title text-primary fw-bold">Top Lapangan</h2>
      </div><!-- End Section Title -->

      <div class="container">

        <div class="row gy-4">

          <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="400">
            <div class="lapangan-item position-relative text-center">
              
              <img src="assets/img/lapangan/purwokerto sport centre.jpg" alt="Purwokerto Sport Centre" class="img-fluid mb-3">
              
              <h3>Purwokerto Sport Centre</h3>
              
              <p>Info booking hanya di aplikasi:</p>
              <a href="https://play.google.com/store/apps/details?id=com.gosportid" target="_blank">
                Gosport Indonesia (Google Play)
              </a>
              
              <p class="mt-3">Info lengkap:</p>
              <a href="https://mybossolution.com/gosport/arena/44/4" target="_blank">
                https://purwokertosportcentre.com
              </a>
              
              <p class="mt-3">
                🥅 Open 24 Jam 🥅<br>
                Jl. Profesor DR. HR Boenyamin, Pakembaran, <br>
                Bancarkembar, Kec. Purwokerto Utara, <br>
                Kabupaten Banyumas, Jawa Tengah 53121, Indonesia
              </p>
              
            </div>
          </div><!-- End Lapangan Item -->
          
          
          <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="200">
             <div class="lapangan-item position-relative text-center">
              <img src="assets/img/lapangan/score futsal.jpg" alt="Score Futsal Center" class="img-fluid mb-3">
              
              <h3>Score Futsal Center</h3>
              
              <p class="mt-3">Info lengkap:</p>
              <a href="https://linktr.ee/Scorefutsalpurwokerto" target="_blank">
                 https://linktr.ee/Scorefutsalpurwokerto</a>
                 
                 <p class="mt-4">🥅 Open at 10.00–23.00 🥅</p>
                 <p>
                  Jl. DR. Soeparno No. 99, Arcawinangun, <br>
                  Kec. Purwokerto Timur, Kabupaten Banyumas</p>
                  
                  <p class="mt-4">🥅 Open at 10.00–00.00 🥅</p>
                  <p>
                    Dusun II, DukuhWaluh, Kec. Kembaran, <br>
                    Kabupaten Banyumas, Jawa Tengah 53182
                  </p>
                
                </div>
              </div><!-- End Lapangan Item -->


              <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="400">
                <div class="lapangan-item position-relative text-center">
                  
                  <img src="assets/img/lapangan/petrofutsal.jpg" alt="Purwokerto Sport Centre" class="img-fluid mb-3">
                  
                  <h3>Petro Futsal</h3>
                  
                  <p>Info :</p>
                  <a href="https://wa.me/6282323972957" target="_blank">Contact Person (0823-2397-2957)</a>
                  <a href="https://wa.me/6285725134837" target="_blank">Contact Person (0857-2513-4837)</a>
                  
                  <p class="mt-3">
                    🥅 Open at 08.00-00.00 🥅<br>
                    Jl. Sunan Bonang No. 1, Dusun II, Dukuhwaluh, <br>
                    Kec. Kembaran, Kabupaten Banyumas, Jawa Tengah 53182
                  </p>
                  
                </div>
              </div><!-- End Lapangan Item -->
              
              
              <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="200">
                <div class="lapangan-item position-relative text-center">
              
              
                  <img src="assets/img/lapangan/orionsportcenter.jpg" alt="Score Futsal Center" class="img-fluid mb-3">
                  <h3>Orion Sport Center</h3></a>
                  
                  <p class="mt-3">Info lengkap dan reservasi :</p>
                  <a href="https://l.instagram.com/?u=https%3A%2F%2Flinktr.ee%2Forionsportcenter_pwt&e=AT187yKb24ATcrDmV0IPQBPGKRSG0EGXX9Z9Py0BKkw0YZg-HmssQ_DdN7j4U3awUp0B_UsTi3l-QnZCgdyiGkUVWPP7-8UO" target="_blank">
                    https://linktr.ee/orionsportcenter</a>
                    
                    <p class="mt-3">
                      🥅 Open at 08.00-23.59 🥅 <br>
                      JL. KH. Wahid Hasyim No.102, Windusara, <br>
                      Karangklesem, Kec. Purwokerto Selatan, Kab. Banyumas<br>
                    </p>      
                  </div>
                </div><!-- End Lapangan Item -->


                <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="200">
                  <div class="lapangan-item position-relative text-center">
                
                
                    <img src="assets/img/lapangan/f530 arena.jpg" alt="Score Futsal Center" class="img-fluid mb-3">
                    <h3>Mini Soccer Field F530 Arena</h3></a>
                    
                    <p class="mt-3">Info lengkap dan reservasi :</p>
                    <a href="https://linktr.ee/f530.arena" target="_blank">
                      https://linktr.ee/f530.arena</a>
                      
                      <p class="mt-3">
                        🥅 Open at 06.00-00.00 🥅 <br>
                        Jl. Raya Beji Karangsalam No. 3, Dusun III,<br>
                        Karangsalam Kidul, Kec. Kedung Banteng, Kabupaten Banyumas<br>
                      </p>      
                    </div>
                  </div><!-- End Lapangan Item -->

          <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="400">
            <div class="lapangan-item position-relative text-center">
              
              <img src="assets/img/lapangan/dynamic.jpg" alt="Smash Badminton" class="img-fluid mb-3">
          
              <h3>Dynamic Sport Canter</h3>
              
              <p>Info booking (MedSos):</p>
              <a href="https://wa.me/628112998484" target="_blank">Cp (08112998484)</a>
              <a href="https://www.instagram.com/dynamic_sport_centre" target="_blank">www.instagram.com/dynamic_sport_centre</a>
          
              <p class="mt-3">
                🥅 Open at 08.00-18.00 🥅 <br>
                Jl. Dr. Gumbreg No. 26, Mersi, Kec. Purwokerto Tim., <br>
                Kabupaten Banyumas, Jawa Tengah 53112
              </p>  
            </div>
          </div><!-- End Lapangan Item -->

          <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="400">
            <div class="lapangan-item position-relative text-center">
              
              <img src="assets/img/lapangan/smash badmin.jpg" alt="Smash Badminton" class="img-fluid mb-3">
          
              <h3>Smash Badminton</h3>
              
              <p>Info booking:</p>
              <a href="https://linktr.ee/smashpwt58" target="_blank">https://linktr.ee/smashpwt58</a>
              
              <p class="mt-2">Info lengkap:</p>
              <a href="https://smashbadmintoncenter.com" target="_blank">https://smashbadmintoncenter.com</a>
          
              <p class="mt-3">
                🥅 Open at 08.00-22.00 🥅<br>
                Jl. Martadireja II, Kepetek, Mersi,<br>
                Kec. Purwokerto Timur,<br>
                Kabupaten Banyumas, Jawa Tengah 53111<br>
              </p>  
            </div>
          </div><!-- End Lapangan Item -->
          

          <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="400">
            <div class="lapangan-item position-relative text-center">
              
              <img src="assets/img/lapangan/gor bulutangkis.jpg" alt="Smash Badminton" class="img-fluid mb-3">
          
              <h3>Gor Bulutangkis Arca Mas</h3>
              
              <p>Info booking (MedSos) :</p>
              <a href="https://wa.me/6281903029344" target="_blank">Cp (081903029344)</a>
              <a href="https://www.instagram.com/gor_bulutangkis__arcamas_pwt" target="_blank">www.instagram.com/gor_bulutangkis__arcamas_pwt</a>
          
              <p class="mt-3">
                🥅 Open at 07.00-23.00 🥅 <br>
                Jl. Watu Gedhe, RT.01/RW.10, Arcawinangun, <br>
                Kec. Purwokerto Tim., Kabupaten Banyumas
              </p>  
            </div>
          </div><!-- End Lapangan Item -->

          <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="400">
            <div class="lapangan-item position-relative text-center">
              
              <img src="assets/img/lapangan/thegardenslam.jpg" alt="Smash Badminton" class="img-fluid mb-3">
          
              <h3>The GardenSlam Tennis Park</h3>
              
              <p class="mt-2">Info lengkap:</p>
              <a href="https://linktr.ee/thegardenslam" target="_blank">linktr.ee/thegardenslam</a>
          
              <p class="mt-3">
                🥅 Open at 05.00-22.00 🥅<br>
                Jl. Raya Karangnangka, Dusun III, Karangnangka,<br>
                 Kec. Kedungbanteng, Kabupaten Banyumas
              </p>  
            </div>
          </div><!-- End Lapangan Item -->


    <!-- Komunitas Section -->
    <section id="komunitas" class="komunitas section light-background">

      <!-- Section Title -->
      <div class="container section-title" data-aos="fade-up">
        <div class="section-badge">KOMUNITAS</div>
        <h2 class="section-main-title text-primary fw-bold">Our Komunitas</h2>
      </div><!-- End Section Title -->

      <div class="container">

        <div class="row gy-4">

          <div class="col-lg-4 col-md-6 d-flex align-items-stretch" data-aos="fade-up" data-aos-delay="400">
            <div class="team-member">
              <div class="member-img">
                <img src="assets/img/team/team-4.jpg" class="img-fluid" alt="">
                <div class="social">
                  <a href="https://x.com/GowesPurwokerto" target="_blank"><i class="bi bi-twitter-x"></i></a>
                  <a href="https://facebook.com/gowespurwokerto" target="_blank"><i class="bi bi-facebook"></i></a>
                  <a href="https://instagram.com/gowespurwokerto" target="_blank"><i class="bi bi-instagram"></i></a>
                </div>
              </div>
              <div class="member-info">
                <h4>Gowes Purwokerto</h4>
                <span>Komunitas Gowes</span>
              </div>
            </div>
          </div><!-- End Komunitas Member -->


          <div class="col-lg-4 col-md-6 d-flex align-items-stretch" data-aos="fade-up" data-aos-delay="100">
            <div class="team-member">
              <div class="member-img">
                <img src="assets/img/team/team-1.jpg" class="img-fluid" alt="">
                <div class="social">
                  <a href="https://x.com/BanyumasRunners" target="_blank"><i class="bi bi-twitter-x"></i></a>
                  <a href="https://facebook.com/BanyumasRunners" target="_blank"><i class="bi bi-facebook"></i></a>
                  <a href="https://instagram.com/banyumasrunners" target="_blank"><i class="bi bi-instagram"></i></a>
                </div>
              </div>
              <div class="member-info">
                <h4>Banyumas Runners</h4>
                <span>Komunitas Lari</span>
              </div>
            </div>
          </div><!-- End Komunitas Member -->

          <div class="col-lg-4 col-md-6 d-flex align-items-stretch" data-aos="fade-up" data-aos-delay="100">
            <div class="team-member">
              <div class="member-img">
                <img src="assets/img/team/team-6.jpg" class="img-fluid" alt="">
                <div class="social">
                  <a href="https://instagram.com/funrunpurwokerto" target="_blank"><i class="bi bi-instagram"></i></a>
                </div>
              </div>
              <div class="member-info">
                <h4>Fun Run Purwokerto</h4>
                <span>Komunitas Lari</span>
              </div>
            </div>
          </div><!-- End Komunitas Member -->

          
          <div class="col-lg-4 col-md-6 d-flex align-items-stretch" data-aos="fade-up" data-aos-delay="200">
            <div class="team-member">
              <div class="member-img">
                <img src="assets/img/team/team-2.jpg" class="img-fluid" alt="">
                <div class="social">
                  <a href="https://www.instagram.com/f4f.id" target="_blank"><i class="bi bi-instagram"></i></a>
                </div>
              </div>
              <div class="member-info">
                <h4>Football 4 Fun</h4>
                <span>Komunitas Sepak Bola</span>
              </div>
            </div>
          </div><!-- End Komunitas Member -->

          <div class="col-lg-4 col-md-6 d-flex align-items-stretch" data-aos="fade-up" data-aos-delay="300">
            <div class="team-member">
              <div class="member-img">
                <img src="assets/img/team/team-3.jpg" class="img-fluid" alt="">
                <div class="social">
                  <a href="https://instagram.com/ssbimpwtofficial" target="_blank"><i class="bi bi-instagram"></i></a>
                </div>
              </div>
              <div class="member-info">
                <h4>SSB IM Purwokerto</h4>
                <span>Komunitas Sepak Bola</span>
              </div>
            </div>
          </div><!-- End Komunitas Member -->

          <div class="col-lg-4 col-md-6 d-flex align-items-stretch" data-aos="fade-up" data-aos-delay="400">
            <div class="team-member">
              <div class="member-img">
                <img src="assets/img/team/team-5.jpg" class="img-fluid" alt="">
                <div class="social">
                  <a href="https://www.instagram.com/bolasatukankita_purwokerto" target="_blank"><i class="bi bi-instagram"></i></a>
                </div>
              </div>
              <div class="member-info">
                <h4>Bola Satukan Kita</h4>
                <span>Komunitas Bola</span>
              </div>
            </div>
          </div><!-- End Komunitas Member -->

        </div>

      </div>

    </section><!-- /Komunitas Section -->

    <!-- Artikel Section -->
    <section id="artikel" class="artikel section" style="background-color: #e6f0f8; padding: 40px 0;">

      <!-- Section Title -->
      <div class="container section-title" data-aos="fade-up">
        <div class="section-badge">ARTIKEL</div>
        <h2 class="section-main-title text-primary fw-bold">Baca Artikel Sehat</h2>
      </div><!-- End Section Title -->

      <div class="container">
        <div class="row gy-4">

          <!-- Artikel 1 -->
          <div class="col-xl-4 col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="100">
            <div class="artikel-item text-center">
              <img src="assets/img/artikel/artikel-1.png" class="img-fluid" alt="Panduan Olahraga">
              <h5 class="mt-3">
                <a href="https://www.rspondokindah.co.id/id/news/raih-manfaatnya--hindari-cederanya" target="_blank">
                  Panduan Olahraga yang Benar untuk Hasil Maksimal dan Kesehatan Optimal
                </a>
              </h5>
            </div>
          </div>
          

          <!-- Artikel 2 -->
          <div class="col-xl-4 col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="100">
            <div class="artikel-item text-center">
              <img src="assets/img/artikel/artikel-2.png" class="img-fluid" alt="Panduan Olahraga">
              <h5 class="mt-3">
                <a href="https://hellosehat.com/kebugaran/tips-olahraga/tidur-setelah-olahraga/" target="_blank">
                  Bolehkah Tidur Setelah Olahraga?
                </a>
              </h5>
            </div>
          </div>

           <!-- Artikel 3 -->
           <div class="col-xl-4 col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="100">
            <div class="artikel-item text-center">
              <img src="assets/img/artikel/artikel-3.png" class="img-fluid" alt="Panduan Olahraga">
              <h5 class="mt-3">
                <a href="https://hellosehat.com/kebugaran/tips-olahraga/cara-untuk-memulai-olahraga/" target="_blank">
                  9 Tips Memulai Olahraga bagi Pemula agar Konsisten
                </a>
              </h5>
            </div>
          </div>

          <!-- Artikel 4 -->
          <div class="col-xl-4 col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="100">
            <div class="artikel-item text-center">
              <img src="assets/img/artikel/artikel-4.png" class="img-fluid" alt="Panduan Olahraga">
              <h5 class="mt-3">
                <a href="https://dinkes.jakarta.go.id/berita/read/tips-dan-manfaat-olahraga-untuk-menjaga-kesehatan-tubuh-kita" target="_blank">
                  Tips dan Manfaat Olahraga untuk Menjaga Kesehatan Tubuh Kita
                </a>
              </h5>
            </div>
          </div>

           <!-- Artikel 5 -->
           <div class="col-xl-4 col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="100">
            <div class="artikel-item text-center">
              <img src="assets/img/artikel/artikel-5.png" class="img-fluid" alt="Panduan Olahraga">
              <h5 class="mt-3">
                <a href="https://www.rsbudimedika.com/tips-olahraga-sehat-untuk-meningkatkan-kesehatan-tubuh/" target="_blank">
                  Tips Olahraga Sehat untuk Meningkatkan Kesehatan Tubuh
                </a>
              </h5>
            </div>
          </div>

          <!-- Artikel 6 -->
          <div class="col-xl-4 col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="100">
            <div class="artikel-item text-center">
              <img src="assets/img/artikel/artikel-6.png" class="img-fluid" alt="Panduan Olahraga">
              <h5 class="mt-3">
                <a href="https://rspj.ihc.id/artikel-detail-405-Pentingnya-Olahraga-bagi-Kesehatan-Masyarakat-Indonesia.html" target="_blank">
                  Pentingnya Olahraga bagi Kesehatan Masyarakat Indonesia
                </a>
              </h5>
            </div>
          </div>

          <!-- Artikel 7 -->
          <div class="col-xl-4 col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="100">
            <div class="artikel-item text-center">
              <img src="assets/img/artikel/artikel-7.png" class="img-fluid" alt="Panduan Olahraga">
              <h5 class="mt-3">
                <a href="https://hellosehat.com/kebugaran/tips-olahraga/tidak-berkeringat-saat-olahraga/" target="_blank">
                  Tidak Berkeringat Saat Olahraga? Kenali 6 Penyebabnya
                </a>
              </h5>
            </div>
          </div>

          <!-- Artikel 8 -->
          <div class="col-xl-4 col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="100">
            <div class="artikel-item text-center">
              <img src="assets/img/artikel/artikel-8.png" class="img-fluid" alt="Panduan Olahraga">
              <h5 class="mt-3">
                <a href="https://ayosehat.kemkes.go.id/5-manfaat-olahraga-bagi-tubuh" target="_blank">
                  5 Manfaat Olahraga Bagi Tubuh
                </a>
              </h5>
            </div>
          </div>

          <!-- Artikel 9 -->
          <div class="col-xl-4 col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="100">
            <div class="artikel-item text-center">
              <img src="assets/img/artikel/artikel-9.png" class="img-fluid" alt="Panduan Olahraga">
              <h5 class="mt-3">
                <a href="https://www.rri.co.id/opini/500175/mulai-hidup-sehat-dengan-berolahraga" target="_blank">
                  Mulai Hidup Sehat Dengan Berolahraga
                </a>
              </h5>
            </div>
          </div>
        </div>
      </div>

    </section><!-- /Artikel Section -->


 

    <div class="container copyright text-center mt-4">
      <p>© <strong class="px-1 sitename">BanyumaSportHub</strong> <span>All Rights Reserved</span></p>
      <div class="credits">
        <!-- All the links in the footer should remain intact. -->
        <!-- You can delete the links only if you've purchased the pro version. -->
        <!-- Licensing information: https://bootstrapmade.com/license/ -->
        <!-- Purchase the pro version with working PHP/AJAX contact form: [buy-url] -->
        Designed by <a>Kelompok 3 IPPL</a>
      </div>
    </div>

  </footer>

  <!-- Scroll Top -->
  <a href="#" id="scroll-top" class="scroll-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

  <!-- Preloader -->
  <div id="preloader">
    <div></div>
    <div></div>
    <div></div>
    <div></div>
  </div>

  <!-- Vendor JS Files -->
  <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="assets/vendor/php-email-form/validate.js"></script>
  <script src="assets/vendor/aos/aos.js"></script>
  <script src="assets/vendor/glightbox/js/glightbox.min.js"></script>
  <script src="assets/vendor/waypoints/noframework.waypoints.js"></script>
  <script src="assets/vendor/purecounter/purecounter_vanilla.js"></script>
  <script src="assets/vendor/swiper/swiper-bundle.min.js"></script>
  <script src="assets/vendor/imagesloaded/imagesloaded.pkgd.min.js"></script>
  <script src="assets/vendor/isotope-layout/isotope.pkgd.min.js"></script>

  <!-- Main JS File -->
  <script src="assets/js/main.js"></script>

  <!-- Product Preview Modal -->
  <div class="modal fade" id="productPreviewModal" tabindex="-1" aria-labelledby="productPreviewModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="productPreviewModalLabel"></h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body row">
          <div class="col-md-6 text-center mb-3 mb-md-0">
            <img id="modalProductImage" src="" alt="" class="img-fluid rounded shadow">
          </div>
          <div class="col-md-6">
            <div class="mb-2">
              <span class="badge bg-primary" id="modalProductStock"></span>
            </div>
            <div class="mb-2">
              <span class="h4 text-primary" id="modalProductPrice"></span>
            </div>
            <div class="mb-3" id="modalProductDescription"></div>
            <form action="cart.php" method="POST" id="modalAddToCartForm">
              <input type="hidden" name="action" value="add">
              <input type="hidden" name="product_id" id="modalProductId" value="">
              <div class="input-group mb-3" style="max-width: 120px;">
                <input type="number" name="quantity" value="1" min="1" class="form-control" id="modalProductQty">
              </div>
              <button type="submit" class="btn btn-primary w-100" id="modalAddToCartBtn">
                <i class="bi bi-cart-plus me-1"></i>Add to Cart
              </button>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>

  <script>
  document.addEventListener('DOMContentLoaded', function() {
    var previewTriggers = document.querySelectorAll('.product-preview-trigger');
    var modalLabel = document.getElementById('productPreviewModalLabel');
    var modalImage = document.getElementById('modalProductImage');
    var modalPrice = document.getElementById('modalProductPrice');
    var modalStock = document.getElementById('modalProductStock');
    var modalDesc = document.getElementById('modalProductDescription');
    var modalId = document.getElementById('modalProductId');
    var modalQty = document.getElementById('modalProductQty');
    var modalAddToCartBtn = document.getElementById('modalAddToCartBtn');
    var modalAddToCartForm = document.getElementById('modalAddToCartForm');

    previewTriggers.forEach(function(trigger) {
      trigger.addEventListener('click', function(e) {
        e.preventDefault();
        var name = this.getAttribute('data-name');
        var price = this.getAttribute('data-price');
        var image = this.getAttribute('data-image');
        var stock = parseInt(this.getAttribute('data-stock'));
        var desc = this.getAttribute('data-description');
        var id = this.getAttribute('data-id');

        modalLabel.textContent = name;
        modalImage.src = image;
        modalPrice.textContent = 'Rp ' + price;
        modalStock.textContent = 'Stok: ' + stock;
        modalDesc.textContent = desc || '-';
        modalId.value = id;
        modalQty.value = 1;
        modalQty.max = stock;

        // Update tampilan berdasarkan stok
        if (stock <= 0) {
          modalStock.className = 'badge bg-danger';
          modalStock.textContent = 'Stok Habis';
          modalQty.disabled = true;
          modalAddToCartBtn.disabled = true;
          modalAddToCartBtn.innerHTML = '<i class="bi bi-x-circle me-1"></i>Stok Habis';
          modalAddToCartBtn.className = 'btn btn-secondary w-100';
        } else {
          modalStock.className = 'badge bg-primary';
          modalQty.disabled = false;
          modalAddToCartBtn.disabled = false;
          modalAddToCartBtn.innerHTML = '<i class="bi bi-cart-plus me-1"></i>Add to Cart';
          modalAddToCartBtn.className = 'btn btn-primary w-100';
        }
      });
    });

    // Validasi quantity saat input
    modalQty.addEventListener('input', function() {
      var max = parseInt(this.max);
      var value = parseInt(this.value);
      
      if (value > max) {
        this.value = max;
      } else if (value < 1) {
        this.value = 1;
      }
    });

    // Validasi form sebelum submit
    modalAddToCartForm.addEventListener('submit', function(e) {
      var stock = parseInt(modalQty.max);
      var quantity = parseInt(modalQty.value);
      
      if (stock <= 0) {
        e.preventDefault();
        alert('Maaf, stok produk ini sudah habis.');
      } else if (quantity > stock) {
        e.preventDefault();
        alert('Maaf, jumlah yang diminta melebihi stok yang tersedia.');
      }
    });

    // Explicitly initialize Bootstrap Dropdown
    var userDropdownElement = document.getElementById('userDropdown');
    if (userDropdownElement) {
      var userDropdown = new bootstrap.Dropdown(userDropdownElement);
    }
  });
  </script>

</body>

</html>

<style>
/* Section Badge & Title Styles (Unified) */
.section-title {
    text-align: center;
    padding-bottom: 30px;
}
.section-badge {
    display: inline-block;
    background: #e6f0f8;
    color: #1e3a8a;
    font-size: 14px;
    font-weight: 700;
    letter-spacing: 1px;
    border-radius: 20px;
    padding: 6px 22px;
    margin-bottom: 18px;
    text-transform: uppercase;
}
.section-main-title {
    font-size: 2.2rem;
    font-weight: 700;
    margin-bottom: 0;
    line-height: 1.2;
}
.section-main-title .text-primary {
    color: #1e3a8a !important;
}
.section-title h2, .section-title p, .section-title .description-title {
    display: none !important;
}
@media (max-width: 768px) {
    .section-main-title {
        font-size: 1.4rem;
    }
}
/* Marketplace Styles */
.marketplace {
    padding: 60px 0;
}

.section-title {
    text-align: center;
    padding-bottom: 30px;
}

.section-badge {
    display: inline-block;
    background: #e6f0f8;
    color: #1e3a8a;
    font-size: 14px;
    font-weight: 700;
    letter-spacing: 1px;
    border-radius: 20px;
    padding: 6px 22px;
    margin-bottom: 18px;
    text-transform: uppercase;
}

.marketplace-title {
    font-size: 2.2rem;
    font-weight: 700;
    margin-bottom: 0;
    line-height: 1.2;
}

.marketplace-title .text-primary {
    color: #1e3a8a !important;
}

.section-title h2 {
    font-size: 32px;
    font-weight: bold;
    text-transform: uppercase;
    margin-bottom: 20px;
    padding-bottom: 20px;
    position: relative;
    color: #1e3a8a;
    display: none; /* Hide default h2 for marketplace */
}

.section-title p {
    margin-bottom: 0;
    font-size: 18px;
    color: #6c757d;
}

.section-title .description-title {
    color: #1e3a8a;
    font-weight: 600;
}

.product-card {
    background: #fff;
    border-radius: 12px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    transition: all 0.3s ease;
    overflow: hidden;
    border: 1px solid rgba(0,0,0,0.05);
}

.product-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 16px rgba(0,0,0,0.12);
}

.product-image {
    position: relative;
    padding-top: 100%;
    overflow: hidden;
    background: #f8f9fa;
}

.product-image img {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.3s ease;
}

.product-card:hover .product-image img {
    transform: scale(1.05);
}

.stock-badge {
    position: absolute;
    top: 12px;
    right: 12px;
    background: rgba(255, 59, 48, 0.95);
    color: white;
    padding: 6px 12px;
    border-radius: 6px;
    font-size: 12px;
    font-weight: 500;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.product-info {
    padding: 20px;
    display: flex;
    flex-direction: column;
    height: calc(100% - 100%);
}

.product-name {
    font-size: 15px;
    font-weight: 500;
    margin-bottom: 10px;
    line-height: 1.4;
    height: 42px;
    overflow: hidden;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
}

.product-name a {
    color: #1e3a8a;
    text-decoration: none;
    transition: color 0.2s ease;
}

.product-name a:hover {
    color: #1e40af;
}

.product-price {
    font-size: 18px;
    font-weight: 600;
    color: #1e3a8a;
    margin-bottom: 10px;
}

.product-meta {
    display: flex;
    justify-content: space-between;
    font-size: 13px;
    color: #6c757d;
    margin-bottom: 15px;
    padding-bottom: 15px;
    border-bottom: 1px solid #f0f0f0;
}

.product-card .btn-primary {
    background: #1e3a8a;
    border-color: #1e3a8a;
    font-weight: 500;
    padding: 10px 16px;
    font-size: 14px;
    border-radius: 8px;
    transition: all 0.2s ease;
}

.product-card .btn-primary:hover {
    background: #1e40af;
    border-color: #1e40af;
    transform: translateY(-1px);
}

@media (max-width: 768px) {
    .marketplace {
        padding: 40px 0;
    }
    
    .section-header h2 {
        font-size: 28px;
    }
    
    .product-card {
        margin-bottom: 20px;
    }
    
    .product-name {
        font-size: 14px;
        height: 40px;
    }
    
    .product-price {
        font-size: 16px;
    }
    
    .product-meta {
        font-size: 12px;
    }
}

@keyframes pulse {
  0% {
    box-shadow: 0 0 0 0 rgba(220, 53, 69, 0.4);
  }
  70% {
    box-shadow: 0 0 0 10px rgba(220, 53, 69, 0);
  }
  100% {
    box-shadow: 0 0 0 0 rgba(220, 53, 69, 0);
  }
}
</style>