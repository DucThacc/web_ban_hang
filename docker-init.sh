#!/bin/bash
# Wait for MySQL to be ready
until mysqladmin ping -h"localhost" --silent; do
    echo 'Waiting for MySQL to be ready...'
    sleep 2
done

echo 'MySQL is ready!'
