<?php 
echo $this->include('officer/layout/view/header.php');
echo $this->include('officer/layout/view/sidebar.php');
echo $this->renderSection('content');
echo $this->include('officer/layout/view/footer.php');
?>