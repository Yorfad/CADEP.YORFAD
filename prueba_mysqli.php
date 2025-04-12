<?php
if (function_exists('mysqli_connect')) {
    echo "✅ mysqli está disponible.";
} else {
    echo "❌ mysqli NO está habilitado.";
}
