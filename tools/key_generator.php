<?php

$encryptionKey = bin2hex(random_bytes(32)); // Повертає 64 символи у hex-форматі
echo $encryptionKey;