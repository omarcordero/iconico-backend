<?php $__env->startComponent('mail::message'); ?>

Hola <?php echo e($user->name); ?>


Su registro ha sido un éxito. Bienvenido a <?php echo e(config('app.name')); ?>.

Gracias,

Equipo <?php echo e(config('app.name')); ?>

<?php echo $__env->renderComponent(); ?>