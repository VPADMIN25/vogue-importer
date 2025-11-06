<?php
// DigitalOcean Health Check file.
// This script MUST return a 200 OK status to pass deployment.
echo "Health check OK. Application is running.";
http_response_code(200);
?>
