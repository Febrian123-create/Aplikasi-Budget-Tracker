#!/bin/bash

PID_DIR="storage/pids"
LOG_DIR="storage/logs"
QUEUE_PID="$PID_DIR/queue.pid"
SCHEDULER_PID="$PID_DIR/scheduler.pid"
QUEUE_LOG="$LOG_DIR/queue.log"
SCHEDULER_LOG="$LOG_DIR/scheduler.log"

stop_services() {
    echo "Stopping services..."
    if [ -f "$QUEUE_PID" ]; then
        kill "$(cat "$QUEUE_PID")" 2>/dev/null && echo "Queue worker stopped." || echo "Queue worker not running."
        rm -f "$QUEUE_PID"
    else
        echo "Queue worker not running."
    fi
    if [ -f "$SCHEDULER_PID" ]; then
        kill "$(cat "$SCHEDULER_PID")" 2>/dev/null && echo "Scheduler stopped." || echo "Scheduler not running."
        rm -f "$SCHEDULER_PID"
    else
        echo "Scheduler not running."
    fi
    exit 0
}

if [ "$1" = "stop" ]; then
    stop_services
fi

# Preflight checks
if ! command -v php &> /dev/null; then
    echo "ERROR: php not found. Make sure PHP is installed and in PATH." && exit 1
fi

if ! php artisan --version &> /dev/null; then
    echo "ERROR: php artisan failed. Are you in the Laravel project root?" && exit 1
fi

APP_KEY=$(grep '^APP_KEY=' .env 2>/dev/null | cut -d'=' -f2-)
if [ -z "$APP_KEY" ] || [ "$APP_KEY" = "base64:" ]; then
    echo "ERROR: APP_KEY is not set in .env. Run: php artisan key:generate" && exit 1
fi

mkdir -p "$PID_DIR" "$LOG_DIR"

# Start queue worker
echo "Starting queue worker..."
nohup php artisan queue:work --sleep=3 --tries=3 >> "$QUEUE_LOG" 2>&1 &
echo $! > "$QUEUE_PID"
echo "Queue worker started (PID: $(cat "$QUEUE_PID"), log: $QUEUE_LOG)"

# Start scheduler
echo "Starting scheduler..."
nohup php artisan schedule:work >> "$SCHEDULER_LOG" 2>&1 &
echo $! > "$SCHEDULER_PID"
echo "Scheduler started (PID: $(cat "$SCHEDULER_PID"), log: $SCHEDULER_LOG)"

echo ""
echo "All services running. To stop: bash start.sh stop"
