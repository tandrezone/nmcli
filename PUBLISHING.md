# Publishing Instructions

## How to Publish this Package to Packagist

### Prerequisites
1. Create a GitHub repository for this package
2. Create an account on [Packagist.org](https://packagist.org)

### Steps to Publish

#### 1. Initialize Git Repository
```bash
cd /home/tiago/nmcli
git init
git add .
git commit -m "Initial commit: nmcli-php package v1.0.0"
```

#### 2. Create GitHub Repository
1. Go to GitHub and create a new repository named `nmcli-php`
2. Add the remote and push:
```bash
git remote add origin https://github.com/tandrezone/nmcli-php.git
git branch -M main
git push -u origin main
```

#### 3. Tag the Release
```bash
git tag -a v1.0.0 -m "Release version 1.0.0"
git push origin v1.0.0
```

#### 4. Submit to Packagist
1. Go to [Packagist.org](https://packagist.org)
2. Click "Submit"
3. Enter your GitHub repository URL: `https://github.com/tandrezone/nmcli-php`
4. Click "Check"
5. If validation passes, click "Submit"

#### 5. Set up Auto-Update (Recommended)
1. In your GitHub repository, go to Settings → Webhooks
2. Add webhook with:
   - Payload URL: `https://packagist.org/api/github?username=YOUR_USERNAME`
   - Content type: `application/json`
   - Secret: Your Packagist API token
   - Events: Just the push event

### Package Installation Instructions for Users

Once published, users can install your package with:

```bash
composer require tandrezone/nmcli-php
```

### Development Installation

For local development, users can also install directly from the repository:

```bash
composer require tandrezone/nmcli-php:dev-main
```

### Version Management

- Follow [Semantic Versioning](https://semver.org/)
- Create git tags for each release
- Update CHANGELOG.md for each version
- Packagist will automatically detect new tags and create releases

### Example Release Process

For future releases:

1. Make changes and commit them
2. Update `CHANGELOG.md` with new version info
3. Commit the changelog
4. Create and push a new tag:
   ```bash
   git tag -a v1.1.0 -m "Release version 1.1.0"
   git push origin v1.1.0
   ```
5. Packagist will automatically detect and publish the new version

### Package Information

- **Name**: `tandrezone/nmcli-php`
- **Type**: `library`
- **License**: `MIT`
- **Minimum PHP**: `7.4`
- **PSR-4 Namespace**: `Tandrezone\NmcliPhp`

### Support and Documentation

- Repository: https://github.com/tandrezone/nmcli-php
- Issues: https://github.com/tandrezone/nmcli-php/issues
- Packagist: https://packagist.org/packages/tandrezone/nmcli-php