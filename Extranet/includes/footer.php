<footer class="p-0 m-0 container-fluid d-flex justify-content-center bg-light border-red-top">
    <div class="col-lg-8 col-12 ">
        <!--Footer-->
            <div class="container-fluid mb-4">
                <div class="row mt-4 mb-5">
                    <!--First column-->
                    <div class="col-12 col-md-3 mb-4 d-flex justify-content-center">
                        <a href="#">
                            <img class="img-fluid height-150" src="/images/style/logo_aristocrate.png" alt="Logo">
                        </a>
                    </div>
                    <!--/.First column-->

                    <!--Second column-->
                    <div class="col-12 col-md-3 mb-4">
                        <div class="container-fluid d-flex justify-content-center justify-content-md-start pb-2 p-0 mb-2 border border-top-0 border-right-0 border-left-0">
                            <h4>Le site</h4>
                        </div>
                        <div class=" m-0 p-0 container-fluid d-flex flex-column align-items-center align-items-md-start">
                            <a href="/">Accueil</a>
                            <a href="/prochainement.php">Qui sommes-nous?</a>
                            <a href="/prochainement.php">Mentions légales</a>
                        </div>
                    </div>
                    <!--/.Second column-->

                    <!--Third column-->
                    <div class="col-12 col-md-3 mb-4">
                        <div class="container-fluid d-flex justify-content-center justify-content-md-start pb-2 p-0 mb-2 border border-top-0 border-right-0 border-left-0">
                            <h4>Mon espace</h4>
                        </div>
                        <div class="container-fluid d-flex flex-column align-items-center align-items-md-start m-0 p-0">
                            <?php
                            if(empty($userSession)){
                                ?>
                                <a data-toggle="modal" data-target="#inscription" href="#">Inscription</a>
                                <?php
                            }
                            else{
                                ?>
                                <a href="/profil.php">Mon compte</a>
                            <?php
                            }
                            ?>
                            <a href="/prochainement.php">Aide</a>
                        </div>
                    </div>
                    <!--/.Third column-->

                    <!--Fourth column-->
                    <div class="col-12 col-md-3 mb-4">
                        <div class="container-fluid d-flex justify-content-center justify-content-md-start  pb-2 p-0 mb-2 border border-top-0 border-right-0 border-left-0">
                            <h4>Contact</h4>
                        </div>
                        <div class="d-flex justify-content-center justify-content-md-start mb-3 p-0 m-0">
                            <a href="mailto:contact@aristocrate.me">contact@aristocrate.me</a>
                        </div>
                        <div class="d-flex justify-content-center justify-content-md-start p-0 m-0">
                            <a href="#" class="pr-2">
                                <img class="imf-fluid height-50 affiche-anim rounded-circle" src="/images/style/fb.png" alt="facebook">
                            </a>
                            <a href="#" class="pr-2">
                                <img class="imf-fluid height-50 affiche-anim rounded-circle" src="/images/style/tw.png" alt="twitter">
                            </a>
                            <a href="#" class="pr-2">
                                <img class="imf-fluid height-50 affiche-anim rounded-circle" src="/images/style/instagram.png" alt="instagram">
                            </a>
                        </div>
                    </div>
                    <!--/.Fourth column-->

                </div>
            </div>
            <!--/.Footer Links-->

            <!--Social buttons-->


            <!--/.Social buttons-->

            <!-- Copyright-->
            <div class="footer-copyright py-3 text-center">
                © 2020 Copyright:
                <a href="aristocrate.me">
                    <strong> aristocrate.me</strong>
                </a>
                <br>
                <span class="text-muted"><small>Le site aristocrate.me est un projet scolaire réalisé dans le cadre de notre formation à Efrei Paris
                et n'est pas encore fonctionnel, ni utilisable.</small></span>
            </div>
    </div>
</footer>
