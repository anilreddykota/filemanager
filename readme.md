<!-- WRITE READ ME FOR THIS OPEN SOURCE A-PANNEL  -->
# A-Pannel Installer
This is the installation guide for A-Pannel, a user-friendly file manager and panel system. Follow the steps below to set up A-Pannel on your server.
## Requirements
- PHP version 7.4 or higher
- PDO extension enabled
- MySQLi extension enabled
- Writable `config.php` file or the `config` directory
- A web server (Apache, Nginx, etc.) with PHP support
- MySQL database
- Composer installed (optional but recommended for dependency management)
- Git (optional but recommended for version control)

## Installation Steps
1. **Download A-Pannel**: Clone the repository or download the latest release from GitHub.
   ```bash
   git clone https://github.com/anilreddykota/filemanager.git
   ``` 
2. **Upload to Server**: Upload the A-Pannel files to your web server's document root or a subdirectory.
3. **Create Database**: Create a new MySQL database for A-Pannel.
4. **Configure Database**: Edit the `config.php` file to set up your database connection.
5. **Run Installer**: Access the installer script via your web browser to complete the installation.
6. **Post-Installation**: After installation, you may want to remove the installer script for security reasons.
7. **Access A-Pannel**: Navigate to the A-Pannel URL in your web browser to start using it.
8. **Configure Settings**: Once A-Pannel is running, you can configure additional settings through the admin panel.
9. **Update Regularly**: Keep A-Pannel updated by pulling the latest changes from the repository or downloading new releases.
10. **Contribute**: If you find bugs or have feature requests, feel free to open an issue on GitHub or submit a pull request.
11. **Support**: If you need help, you can reach out to the community or check the documentation for assistance.
## Troubleshooting
If you encounter issues during installation, check the following:
- Ensure all requirements are met.
- Check file permissions for the `config.php` file or the `config` directory.
- Verify your database connection details in the `config.php` file.
- Check the web server error logs for any relevant error messages.
- Make sure the web server is configured to serve PHP files correctly.
- If using Composer, ensure all dependencies are installed by running `composer install` in the project root.
- Check for any .htaccess file issues if using Apache.
- If using Nginx, ensure the server block is configured correctly to handle PHP files.
- If you are using a custom domain, ensure DNS settings are correctly configured to point to your server.
- If you are using SSL, ensure your SSL certificate is correctly installed and configured.
- If you are using a firewall, ensure that it is not blocking access to your server.
- If you are using a CDN, ensure that it is correctly configured to serve your files.
- If you are using a caching plugin, clear the cache to ensure you are seeing the latest changes.
- If you are using a custom theme or plugin, ensure it is compatible with the current version of A-Pannel.
- If you are using a custom configuration, ensure it is correctly set up in the `config.php` file.
- If you are using a custom database prefix, ensure it is correctly set in the `config.php` file.
- If you are using a custom session handler, ensure it is correctly configured in the `config.php` file.
  
## License
A-Pannel is open-source software licensed under the MIT License. Feel free to modify and distribute it as per the license terms.

## Recent Edits
    These are the recent edits made to the A-Pannel installer files. The changes include updates to the API and various steps in the installation process to improve clarity and functionality.

## Recent Changes
    - Improved error handling during installation.
    - Updated database connection settings.
    - Enhanced user interface for the admin panel.
    - Fixed bugs reported by users in the issue tracker.
    - Refactored code for better maintainability.
  
## Contributing
If you would like to contribute to A-Pannel, please follow these steps:
1. Fork the repository on GitHub.
2. Create a new branch for your feature or bug fix.
3. Make your changes and commit them with clear messages.
4. Push your changes to your forked repository.
5. Submit a pull request to the main repository.

## Contact
If you have any questions or need support, you can contact the A-Pannel team via the following channels:
- **Email**: connect@anisol.co.in

