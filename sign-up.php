<?php require('inc/header.php');
// $query = new query();

if (isset($_POST['register'])) {
   // $email = $_POST['email'];
   // $user_count = count($query->fetchData("users", "user_email", "user_email='$email'"));
   // if ($user_count > 0) {
   //    echo "user already exists with same email";
   // } else {
      // $key = rndmString(6, "", "1234567890");
      // $pass = $_POST['password'];
      // $pass = $auth->encPass($pass);
      $status_msg = "Register Successfully";
      $username = $_POST['name'];
      $email = $_POST['email'];
      $password = $_POST['password'];
      $re_password = $_POST['re_password'];

      if (empty($email)) {
         $status_msg = "Email is required";
      } else if (empty($password)) {
         $status_msg = "Password is required";
      } else if ($password != $re_password) {
         $status_msg = "Password validation failed";
      } else {
         // $status_msg = $auth->loginUser($email, $password);
         $data = ["password" => $password, "password_confirmation" => $re_password, "email" => $email, "name" => $username];
      // $created_user_id = $query->insertData("users", $data);
      // $ex_user = $_SESSION['user_id'];
      // $_SESSION['user_id'] = $created_user_id;
      // $user_id = $_SESSION['user_id'];
      // $_SESSION['user_login'] = true;
      // $cart_update = $query->updateData("cart", "user_id='$user_id'", "user_id='$ex_user'");
         $status_msg = $auth->registerUser($data);
      }

   // }
}
if ($auth->isLogin()) {
   header("location: index.php");
   die();
}
?>
<!-- header area end -->

<!-- cart mini area start -->
<?php require('inc/cart.php'); 

?>

<div class="body-overlay"></div>
<!-- cart mini area end -->



<!-- sidebar area start -->
<div class="sidebar__area">
   <div class="sidebar__wrapper">
      <div class="sidebar__close">
         <button class="sidebar__close-btn" id="sidebar__close-btn">
            <span><i class="fal fa-times"></i></span>
            <span>close</span>
         </button>
      </div>
      <div class="sidebar__content">
         <div class="logo mb-40">
            <a href="index.php">
               <img class="logo" src="assets/img/logo/Online-store-white.png" alt="logo">
            </a>
         </div>
         <div class="mobile-menu"></div>
         <div class="sidebar__action mt-330">
            <div class="sidebar__login mt-15">
               <!-- <a href="#"><i class="far fa-unlock"></i> Log In</a> -->
                    <?php if ($auth->isLogin()) {
                        echo "<a href=\"account\"><i class=\"far fa-user\"></i>Account</a>";
                    } else {
                        echo "<a href=\"sign-in.php\"><i class=\"far fa-unlock\"></i> Log In</a>";
                    } ?>
            </div>
            <div class="sidebar__cart mt-20">
               <a href="javascript:void(0);" class="cart-toggle-btn">
                  <i class="far fa-shopping-cart"></i>
                  <span>2</span>
               </a>
            </div>
         </div>
      </div>
   </div>
</div>
<!-- sidebar area end -->
<div class="body-overlay"></div>
<!-- sidebar area end -->

<main>

   <!-- bg shape area start -->
   <div class="bg-shape">
      <img src="assets/img/shape/shape-1.png" alt="">
   </div>
   <!-- bg shape area end -->

   <!-- sign up area start -->
   <section class="signup__area po-rel-z1 pt-100 pb-145">
      <div class="sign__shape">
         <img class="man-1" src="assets/img/icon/sign/man-3.png" alt="">
         <img class="man-2 man-22" src="assets/img/icon/sign/man-2.png" alt="">
         <img class="circle" src="assets/img/icon/sign/circle.png" alt="">
         <img class="zigzag" src="assets/img/icon/sign/zigzag.png" alt="">
         <img class="dot" src="assets/img/icon/sign/dot.png" alt="">
         <img class="bg" src="assets/img/icon/sign/sign-up.png" alt="">
         <img class="flower" src="assets/img/icon/sign/flower.png" alt="">
      </div>
      <div class="container">
         <div class="row">
            <div class="col-xxl-8 offset-xxl-2 col-xl-8 offset-xl-2">
               <div class="page__title-wrapper text-center mb-55">
                  <h2 class="page__title-2">Create a free <br> Account</h2>
                  <p>I'm a subhead that goes with a story.</p>
               </div>
            </div>
         </div>
         <div class="row">
            <div class="col-xxl-6 offset-xxl-3 col-xl-6 offset-xl-3 col-lg-8 offset-lg-2">
               <div class="sign__wrapper white-bg">
                  <div class="sign__header mb-35">
                     <div class="sign__in text-center">
                     <!-- <a href="#" class="sign__social g-plus text-start mb-15"><i class="fab fa-google-plus-g"></i>Sign Up with Google</a> -->
                        <?php 
                           $sign_up_msg = '<p> <span>........</span><a href="sign-up.php">sign up</a> with your email<span> ........</span> </p>';
                           if (isset($_POST['register'])) {
                              echo "<p style=\"color:red\"> <span>........</span> {$status_msg}<span> ........</span> </p>";
                           }else
                           echo $sign_up_msg;
                        ?>

                     </div>
                  </div>
                  <div class="sign__form">
                     <?php $form = " <form method=\"POST\" >
                        <div class=\"sign__input-wrapper mb-25\">
                           <h5>Full Name</h5>
                           <div class=\"sign__input\">
                              <input name=\"name\" type=\"text\" placeholder=\"Full name\">
                              <i class=\"fal fa-user\"></i>
                           </div>
                        </div>
                        <!-- <div class=\"sign__input-wrapper mb-10\">
                           <h5>Re-Password</h5>
                           <div class=\"sign__input\">
                              <input type=\"text\" placeholder=\"Re-Password\">
                              <i class=\"fal fa-lock\"></i>
                           </div>
                        </div> -->
                        <div class=\"sign__input-wrapper mb-25\">
                           <h5>Your email</h5>
                           <div class=\"sign__input\">
                              <input name=\"email\" type=\"text\" placeholder=\"e-mail address\">
                              <i class=\"fal fa-envelope\"></i>
                           </div>
                        </div>
                        <div class=\"sign__input-wrapper mb-25\">
                           <h5>Password</h5>
                           <div class=\"sign__input\">
                              <input name=\"password\" type=\"password\" placeholder=\"Password\">
                              <i class=\"fal fa-lock\"></i>
                           </div>
                        </div>
                        <div class=\"sign__input-wrapper mb-10\">
                           <h5>Re-Password</h5>
                           <div class=\"sign__input\">
                              <input name=\"re_password\" type=\"text\" placeholder=\"Re-Password\">
                              <i class=\"fal fa-lock\"></i>
                           </div>
                        </div> 
                        <div class=\"sign__action d-flex justify-content-between mb-30\">
                           <div class=\"sign__agree d-flex align-items-center\">
                              <input class=\"m-check-input\" name=\"term\" type=\"checkbox\" id=\"m-agree\">
                              <label class=\"m-check-label\" for=\"m-agree\">I agree to the <a href=\"#\">Terms & Conditions</a>
                              </label>
                           </div>
                        </div>
                        <button name=\"register\" class=\"m-btn m-btn-4 w-100\"> <span></span> Sign Up</button>
                        <div class=\"sign__new text-center mt-20\">
                           <p>Already in GPLdog ? <a href=\"sign-in.php\"> Sign In</a></p>
                        </div>
                     </form>";

                     
                     if (isset($_GET['auth']) == "verify") {
                        $form = "<form method=\"POST\" >
                           <div class=\"sign__input-wrapper mb-25\">
                              <h5>OTP Verification</h5>
                              <div class=\"sign__input\">
                                 <input name=\"otp\" type=\"text\" placeholder=\"Enter OTP\">
                                 <i class=\"fal fa-lock\"></i>
                              </div>
                             
                           </div>
                     
                           <button name=\"verify\" class=\"m-btn m-btn-4 w-100\"> <span></span> Verify Now</button>
                           
                        </form>";
                        if (isset($_GET['id'])) {
                           $created_user_id = $_GET['id'];
                        } else if (isset($_POST['register'])) {
                           $created_user_id = $created_user_id??null;
                        } else if (isset($_POST['verify'])) {
                           $user_token = isset($_SESSION['user_id'])?$_SESSION['user_id'] : null;

                           // $key = $_POST['otp'];
                           // $q = $query->fetchData("users", "user_activation_key", "ID='$user_id'");

                           // if ($q[0]['user_activation_key'] == $key) {
                           //    $q = $query->updateData("users", "user_status=1,user_activation_key=''", "ID='$user_id'");
                           // } else {
                           //    echo "Wrong OTP";
                           // }
                           if($user_token){
                              header("location: sign-in.php");
                              die();
                           }else {
                              echo "Wrong OTP";
                           }
                        }
                     }
                     echo $form;


                     ?>
                  </div>
               </div>
            </div>
         </div>
      </div>
   </section>
   <!-- sign up area end -->

</main>

<!-- footer area start -->
<?php include("inc/footer.php"); ?>
<!-- footer area start -->
</body>

</html>