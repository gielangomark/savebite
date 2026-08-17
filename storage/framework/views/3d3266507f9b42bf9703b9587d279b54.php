<!DOCTYPE html>
<html lang="<?php echo e(str_replace('_', '-', app()->getLocale())); ?>">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">

    <title><?php echo e(config('app.name', 'SurplusBite')); ?> - Login</title>

    <!-- Fonts & Tailwind -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=plus-jakarta-sans:400,500,600,700,800&display=swap" rel="stylesheet" />
    <script src="https://cdn.tailwindcss.com"></script>

    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }
        .bg-pattern {
            background-color: #f7f3eb;
            background-image: radial-gradient(circle at 10% 20%, rgba(200, 180, 150, 0.15) 0%, transparent 40%),
                              radial-gradient(circle at 90% 80%, rgba(200, 180, 150, 0.15) 0%, transparent 40%);
        }
    </style>
</head>
<body class="bg-pattern text-slate-800 antialiased min-h-screen flex items-center justify-center p-4 sm:p-6 md:p-10">
    <?php echo e($slot); ?>

</body>
</html><?php /**PATH C:\xampp\htdocs\surplusbite\resources\views/layouts/guest.blade.php ENDPATH**/ ?>