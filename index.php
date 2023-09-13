<?php require "includes/header.php"; ?>
    <?php if(isset($_SESSION['username'])) :?>
    <?php echo "Hello ". $_SESSION['username']; ?>
    <?php endif;?>
<?php require "includes/footer.php"; ?>
