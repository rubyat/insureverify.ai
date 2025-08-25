<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

// Marketing and public routes moved to routes/frontend.php


require __DIR__.'/settings.php';
require __DIR__.'/auth.php';

// Frontend (customer) routes
require __DIR__.'/frontend.php';

// Admin and Super Admin routes
require __DIR__.'/admin.php';

// Filemanager routes
require __DIR__.'/filemanager.php';

// Public plans listing moved to routes/frontend.php

// Subscription and other authenticated customer routes moved to routes/frontend.php
