    </div> <!-- Fim do container -->

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Scripts customizados -->
    <?php
    $base_path = (strpos($_SERVER['PHP_SELF'], '/pages/') !== false) ? '../' : '';
    ?>
    <script src="<?php echo $base_path; ?>assets/js/main.js"></script>
</body>
</html>

