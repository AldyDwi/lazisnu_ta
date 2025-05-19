<?php 
echo $this->include('super_admin/layout/view/header.php');
echo $this->include('super_admin/layout/view/sidebar.php');
echo $this->renderSection('content');
echo $this->include('super_admin/layout/view/footer.php');
?>