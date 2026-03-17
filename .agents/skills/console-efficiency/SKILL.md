---
name: console-efficiency
description: >-
  Optimizes terminal command workflows to prevent getting stuck, waiting unnecessarily, or mismanaging background tasks.
  Activates when running Artisan commands, migration status checks, or any long-running terminal processes.
---

# Console Efficiency

## When to Apply

Activate this skill when:
- Executing terminal commands via `run_command`.
- Monitoring background processes using `command_status`.
- Handling multi-step command sequences (e.g., migrate -> seed -> test).

## Best Practices

### 1. Avoid Being "Stuck"
- **Use `WaitMsBeforeAsync` effectively**: If a command is expected to be near-instant (like `php artisan about`), set `WaitMsBeforeAsync` to a reasonable value (e.g., 500-1000ms) to get the result immediately without needing a separate `command_status` call.
- **Immediate Status Check**: For background commands, call `command_status` immediately after `run_command` if you need the initial output or confirmation of startup.

### 2. Smart Monitoring
- **Optimize `WaitDurationSeconds`**: 
    - Use small values (1-2s) for commands expected to finish quickly.
    - Use larger values (10-30s) only for known long-running tasks like `composer install` or `pnpm run build`.
- **Don't Poll Indefinitely**: If a command is `DONE`, process the output and move to the next task. Do not call `command_status` on a completed process.

### 3. Error Recovery
- **Verify Command IDs**: Always double-check the Command ID from the `run_command` output before calling `command_status`.
- **Handle "Command Not Found"**: If a status check fails, verify the project context (CWD) and available resources.

### 4. Efficient Workflow
- **Parallelize where safe**: Run non-conflicting commands in the background while performing file edits or research.
- **Batching**: Group related artisan commands if possible, or use `&&` to chain them in a single `run_command` call if they are sequential and simple.

## Common Pitfalls
- **Waiting too long between checks**: Causes the agent to feel "stalled".
- **Misinterpreting `DONE` status**: Calling status again on a finished command instead of moving on.
- **Ignoring stderr**: Always check for errors in the output before assuming success.
