<?php if (isset($component)) { $__componentOriginal69dc84650370d1d4dc1b42d016d7226b = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal69dc84650370d1d4dc1b42d016d7226b = $attributes; } ?>
<?php $component = App\View\Components\GuestLayout::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('guest-layout'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\App\View\Components\GuestLayout::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
    <!-- Main Card Container -->
    <div class="max-w-5xl w-full bg-white rounded-[32px] border border-slate-900/10 shadow-2xl overflow-hidden grid grid-cols-1 md:grid-cols-2 relative min-h-[620px]">
        
        <!-- LEFT COLUMN: Form Section -->
        <div class="p-8 sm:p-10 flex flex-col justify-between relative z-10 bg-white">
            
            <div>
                <!-- Brand Logo -->
                <div class="flex items-center gap-2.5 mb-6">
                    <div class="w-8 h-8 rounded-lg bg-emerald-800 flex items-center justify-center text-white font-black text-sm shadow-sm">
                        🌱
                    </div>
                    <span class="text-xl font-black text-slate-900 tracking-tight">SurplusBite</span>
                </div>

                <!-- Title Header -->
                <div class="space-y-1 mb-6">
                    <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight">Create Your Account</h1>
                    <p class="text-xs font-medium text-slate-500">Start saving food and join the community.</p>
                </div>

                <!-- Register Form -->
                <form method="POST" action="<?php echo e(route('register')); ?>" class="space-y-4">
                    <?php echo csrf_field(); ?>
                    <input type="hidden" name="redirect" value="<?php echo e(request()->query('redirect', route('dashboard', absolute: false))); ?>">

                    <!-- Name Input -->
                    <div class="space-y-1">
                        <label for="name" class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider">Full Name</label>
                        <input id="name" 
                               type="text" 
                               name="name" 
                               value="<?php echo e(old('name')); ?>" 
                               required 
                               autofocus 
                               autocomplete="name"
                               placeholder="Full name"
                               class="w-full rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-medium text-slate-800 placeholder-slate-300 focus:border-emerald-600 focus:ring-2 focus:ring-emerald-600/20 transition outline-none" />
                        <?php if (isset($component)) { $__componentOriginalf94ed9c5393ef72725d159fe01139746 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalf94ed9c5393ef72725d159fe01139746 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.input-error','data' => ['messages' => $errors->get('name'),'class' => 'mt-1 text-xs text-red-500']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('input-error'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['messages' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($errors->get('name')),'class' => 'mt-1 text-xs text-red-500']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalf94ed9c5393ef72725d159fe01139746)): ?>
<?php $attributes = $__attributesOriginalf94ed9c5393ef72725d159fe01139746; ?>
<?php unset($__attributesOriginalf94ed9c5393ef72725d159fe01139746); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalf94ed9c5393ef72725d159fe01139746)): ?>
<?php $component = $__componentOriginalf94ed9c5393ef72725d159fe01139746; ?>
<?php unset($__componentOriginalf94ed9c5393ef72725d159fe01139746); ?>
<?php endif; ?>
                    </div>

                    <!-- Email Input -->
                    <div class="space-y-1">
                        <label for="email" class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider">Email Address</label>
                        <input id="email" 
                               type="email" 
                               name="email" 
                               value="<?php echo e(old('email')); ?>" 
                               required 
                               autocomplete="username"
                               placeholder="nama@email.com"
                               class="w-full rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-medium text-slate-800 placeholder-slate-300 focus:border-emerald-600 focus:ring-2 focus:ring-emerald-600/20 transition outline-none" />
                        <?php if (isset($component)) { $__componentOriginalf94ed9c5393ef72725d159fe01139746 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalf94ed9c5393ef72725d159fe01139746 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.input-error','data' => ['messages' => $errors->get('email'),'class' => 'mt-1 text-xs text-red-500']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('input-error'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['messages' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($errors->get('email')),'class' => 'mt-1 text-xs text-red-500']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalf94ed9c5393ef72725d159fe01139746)): ?>
<?php $attributes = $__attributesOriginalf94ed9c5393ef72725d159fe01139746; ?>
<?php unset($__attributesOriginalf94ed9c5393ef72725d159fe01139746); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalf94ed9c5393ef72725d159fe01139746)): ?>
<?php $component = $__componentOriginalf94ed9c5393ef72725d159fe01139746; ?>
<?php unset($__componentOriginalf94ed9c5393ef72725d159fe01139746); ?>
<?php endif; ?>
                    </div>

                    <!-- Password & Confirm Password Grid -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div class="space-y-1">
                            <label for="password" class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider">Password</label>
                            <input id="password" 
                                   type="password" 
                                   name="password" 
                                   required 
                                   autocomplete="new-password"
                                   placeholder="••••••••"
                                   class="w-full rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-medium text-slate-800 placeholder-slate-300 focus:border-emerald-600 focus:ring-2 focus:ring-emerald-600/20 transition outline-none" />
                            <?php if (isset($component)) { $__componentOriginalf94ed9c5393ef72725d159fe01139746 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalf94ed9c5393ef72725d159fe01139746 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.input-error','data' => ['messages' => $errors->get('password'),'class' => 'mt-1 text-xs text-red-500']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('input-error'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['messages' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($errors->get('password')),'class' => 'mt-1 text-xs text-red-500']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalf94ed9c5393ef72725d159fe01139746)): ?>
<?php $attributes = $__attributesOriginalf94ed9c5393ef72725d159fe01139746; ?>
<?php unset($__attributesOriginalf94ed9c5393ef72725d159fe01139746); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalf94ed9c5393ef72725d159fe01139746)): ?>
<?php $component = $__componentOriginalf94ed9c5393ef72725d159fe01139746; ?>
<?php unset($__componentOriginalf94ed9c5393ef72725d159fe01139746); ?>
<?php endif; ?>
                        </div>

                        <div class="space-y-1">
                            <label for="password_confirmation" class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider">Confirm</label>
                            <input id="password_confirmation" 
                                   type="password" 
                                   name="password_confirmation" 
                                   required 
                                   autocomplete="new-password"
                                   placeholder="••••••••"
                                   class="w-full rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-medium text-slate-800 placeholder-slate-300 focus:border-emerald-600 focus:ring-2 focus:ring-emerald-600/20 transition outline-none" />
                            <?php if (isset($component)) { $__componentOriginalf94ed9c5393ef72725d159fe01139746 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalf94ed9c5393ef72725d159fe01139746 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.input-error','data' => ['messages' => $errors->get('password_confirmation'),'class' => 'mt-1 text-xs text-red-500']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('input-error'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['messages' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($errors->get('password_confirmation')),'class' => 'mt-1 text-xs text-red-500']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalf94ed9c5393ef72725d159fe01139746)): ?>
<?php $attributes = $__attributesOriginalf94ed9c5393ef72725d159fe01139746; ?>
<?php unset($__attributesOriginalf94ed9c5393ef72725d159fe01139746); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalf94ed9c5393ef72725d159fe01139746)): ?>
<?php $component = $__componentOriginalf94ed9c5393ef72725d159fe01139746; ?>
<?php unset($__componentOriginalf94ed9c5393ef72725d159fe01139746); ?>
<?php endif; ?>
                        </div>
                    </div>

                    <!-- Register As (Role Selection) -->
                    <div class="space-y-1.5 pt-1">
                        <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider">Register as</label>
                        <div class="grid grid-cols-2 gap-3">
                            <label class="flex items-center gap-2.5 rounded-xl border border-slate-200 bg-slate-50/70 p-2.5 cursor-pointer hover:border-emerald-500 transition has-[:checked]:border-emerald-600 has-[:checked]:bg-emerald-50/50">
                                <input type="radio" name="role" value="student" checked class="w-4 h-4 text-emerald-800 focus:ring-emerald-600 cursor-pointer">
                                <span class="text-xs font-bold text-slate-700">🎓 Buyer</span>
                            </label>

                            <label class="flex items-center gap-2.5 rounded-xl border border-slate-200 bg-slate-50/70 p-2.5 cursor-pointer hover:border-emerald-500 transition has-[:checked]:border-emerald-600 has-[:checked]:bg-emerald-50/50">
                                <input type="radio" name="role" value="merchant" class="w-4 h-4 text-emerald-800 focus:ring-emerald-600 cursor-pointer">
                                <span class="text-xs font-bold text-slate-700">🏪 Merchant</span>
                            </label>
                        </div>
                        <?php if (isset($component)) { $__componentOriginalf94ed9c5393ef72725d159fe01139746 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalf94ed9c5393ef72725d159fe01139746 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.input-error','data' => ['messages' => $errors->get('role'),'class' => 'mt-1 text-xs text-red-500']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('input-error'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['messages' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($errors->get('role')),'class' => 'mt-1 text-xs text-red-500']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalf94ed9c5393ef72725d159fe01139746)): ?>
<?php $attributes = $__attributesOriginalf94ed9c5393ef72725d159fe01139746; ?>
<?php unset($__attributesOriginalf94ed9c5393ef72725d159fe01139746); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalf94ed9c5393ef72725d159fe01139746)): ?>
<?php $component = $__componentOriginalf94ed9c5393ef72725d159fe01139746; ?>
<?php unset($__componentOriginalf94ed9c5393ef72725d159fe01139746); ?>
<?php endif; ?>
                    </div>

                    <!-- Buttons Row: Sign up & Login -->
                    <div class="flex items-center gap-3 pt-3">
                        <button type="submit" 
                                class="px-8 py-3 rounded-xl bg-[#0d5c58] hover:bg-[#094643] text-white font-bold text-sm shadow-md shadow-emerald-900/10 transition-all duration-200 active:scale-95">
                            Sign up
                        </button>

                        <a href="<?php echo e(route('login')); ?>" 
                           class="px-6 py-3 rounded-xl border border-slate-300 bg-white hover:bg-slate-50 text-slate-700 font-bold text-sm transition-all duration-200 active:scale-95 text-center">
                            Login
                        </a>
                    </div>
                </form>
            </div>

            <!-- Policy Footer -->
            <div class="pt-6">
                <p class="text-[11px] text-slate-400 leading-relaxed">
                    By sign up you agree to our <a href="#" class="underline text-slate-500 hover:text-slate-800">terms</a> and that you have read our <a href="#" class="underline text-slate-500 hover:text-slate-800">data policy</a>.
                </p>
            </div>
        </div>

        <!-- FLOATING YELLOW CIRCLE BUTTON (Center Divider) -->
        <div class="hidden md:flex absolute left-1/2 -translate-x-1/2 top-1/2 -translate-y-1/2 z-30">
            <button type="button" class="w-10 h-10 rounded-full bg-amber-400 hover:bg-amber-500 text-white shadow-lg flex items-center justify-center transition-transform hover:scale-110 active:scale-95">
                <svg class="w-4 h-4 fill-current ml-0.5 text-white" viewBox="0 0 24 24">
                    <path d="M8 5v14l11-7z"/>
                </svg>
            </button>
        </div>

        <!-- RIGHT COLUMN: Nature & Bird Visual Banner -->
        <div class="relative hidden md:block bg-emerald-950 overflow-hidden">
            <img src="https://images.unsplash.com/photo-1542273917363-3b1817f69a2d?auto=format&fit=crop&w=1200&q=80" 
                 alt="Nature Eco SurplusBite" 
                 class="w-full h-full object-cover opacity-90 transition-transform duration-700 hover:scale-105">

            <div class="absolute inset-0 bg-gradient-to-t from-emerald-950/70 via-transparent to-black/20"></div>

            <!-- Leaf Overlay Pattern -->
            <div class="absolute inset-0 pointer-events-none flex items-center justify-center opacity-30">
                <svg class="w-3/4 h-3/4 text-white" viewBox="0 0 100 100" fill="none" stroke="currentColor" stroke-width="0.8">
                    <path d="M50 10 C 20 40, 20 70, 50 90 C 80 70, 80 40, 50 10 Z" />
                    <path d="M50 10 L 50 90" />
                    <path d="M30 40 Q 50 50 70 40" />
                    <path d="M25 60 Q 50 70 75 60" />
                </svg>
            </div>

            <!-- Bottom Right Slider Control -->
            <div class="absolute bottom-6 right-6 flex items-center gap-2 bg-white/90 backdrop-blur-md px-3 py-1.5 rounded-lg shadow-lg text-slate-800">
                <button type="button" class="text-slate-400 hover:text-slate-900 font-bold text-xs px-1">&larr;</button>
                <span class="text-xs font-bold px-1">1</span>
                <button type="button" class="text-slate-400 hover:text-slate-900 font-bold text-xs px-1">&rarr;</button>
            </div>
        </div>

    </div>
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal69dc84650370d1d4dc1b42d016d7226b)): ?>
<?php $attributes = $__attributesOriginal69dc84650370d1d4dc1b42d016d7226b; ?>
<?php unset($__attributesOriginal69dc84650370d1d4dc1b42d016d7226b); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal69dc84650370d1d4dc1b42d016d7226b)): ?>
<?php $component = $__componentOriginal69dc84650370d1d4dc1b42d016d7226b; ?>
<?php unset($__componentOriginal69dc84650370d1d4dc1b42d016d7226b); ?>
<?php endif; ?><?php /**PATH C:\xampp\htdocs\surplusbite\resources\views/auth/register.blade.php ENDPATH**/ ?>