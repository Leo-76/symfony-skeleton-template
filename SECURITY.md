# Security Policy

## Supported Versions

| Version | 
|---------|
| 1.x     | 

## Reporting a Vulnerability

**Please do not open a public GitHub issue for security vulnerabilities.**

Send an email to **security@example.com** with:

- A description of the vulnerability
- Steps to reproduce
- Potential impact
- Any suggested fixes (optional)

You will receive a response within **48 hours** and a fix will be prioritized.

## Security Best Practices for Users

- Always keep `APP_SECRET` long, random, and private
- Never commit `.env.local` or `.env.prod` to version control
- Run `make security` regularly to audit dependencies
- Keep Symfony and all packages up to date
- Enable HTTPS in production
