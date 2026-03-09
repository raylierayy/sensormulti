# sensormulti

A multi-sensor monitoring and management system.

---

## 🔄 How to Update Your Local Files via VS Code Terminal

Follow these steps to pull the latest changes from GitHub into your local copy using the **VS Code integrated terminal**.

### Step 1 — Open the Terminal in VS Code

- Press **Ctrl + `` ` ``** (backtick) on Windows/Linux  
- Press **Cmd + `` ` ``** on macOS  
- Or go to **Terminal → New Terminal** from the top menu bar

### Step 2 — Navigate to Your Project Folder

If you are not already inside the project directory, navigate to it:

```bash
cd path/to/sensormulti
```

> **Tip:** If VS Code is already open with the project folder, the terminal will start inside the correct directory automatically.

### Step 3 — Check the Current Status (Optional)

See which branch you are on and whether there are any local changes:

```bash
git status
```

### Step 4 — Pull the Latest Changes from GitHub

Download and apply the latest updates from the remote repository:

```bash
git pull
```

If you are working on a specific branch, you can be explicit:

```bash
git pull origin main
```

Replace `main` with your branch name if needed (e.g., `master`, `dev`).

### Step 5 — Verify the Update

After pulling, confirm that your files are up to date:

```bash
git log --oneline -5
```

This shows the five most recent commits, including any that were just pulled in.

---

## ⚠️ Common Situations

### You have local changes that conflict with the pull

If you get a merge conflict error, you can either:

1. **Stash your local changes**, pull, then re-apply them:
   ```bash
   git stash
   git pull
   git stash pop
   ```

2. **Discard your local changes** (⚠️ this is permanent):
   ```bash
   git restore .
   git pull
   ```

### You are on the wrong branch

Switch to the correct branch before pulling:

```bash
git checkout main
git pull
```

---

## 🚀 Quick Reference

| Task | Command |
|---|---|
| Check status | `git status` |
| Pull latest changes | `git pull` |
| Pull from a specific branch | `git pull origin <branch>` |
| See recent commits | `git log --oneline -5` |
| Switch branch | `git checkout <branch>` |
| Stash local changes | `git stash` |
| Restore stashed changes | `git stash pop` |
