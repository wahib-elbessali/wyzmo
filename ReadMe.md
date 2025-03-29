# Wyzmo - Collaborative Project Management Platform

A full-stack web application designed to streamline academic project management with role-based access control, real-time collaboration, and task tracking features.


## 🚨 Project Context  
*This application was developed as part of an academic program with strict time constraints. While it demonstrates core functionality, please note:*  

- 🎓 **Primary purpose**: Educational demonstration and learning exercise  
- ⏳ **Development timeline**: Built within academic time constraints  
- ⚠️ **Implementation notes**: Contains simplified solutions for deadline compliance  
- 🐛 **Testing limitations**: Edge cases may not be fully covered  
- 🛠️ **Architecture**: Reflects academic requirements rather than industry best practices  

*We welcome contributors to help evolve this into a more robust solution!*

## Features ✨

### Role-Based Access Control
- **Admin**: System oversight and analytics dashboard
- **Professors**: Project creation and team management
- **Students**: Task execution and document collaboration

### Core Functionalities
| Feature | Description |
|---------|-------------|
| 📂 Projects | Create/manage projects with deadlines and descriptions |
| 📝 Kanban Board | Visual task management (Todo/In Progress/Done) |
| 👥 Team Collaboration | Add collaborators and set representatives |
| 📊 Progress Tracking | Automatic percentage calculations with visual indicators |
| 📁 Document Hub | File sharing with project-specific storage |
| 💬 Real-time Chat | Integrated messaging with history |
| 🔍 Smart Search | Find projects and team members quickly |

### Technical Highlights
- Hand-drawn sketch-style UI theme
- Real-time updates via WebSockets (Ably.io)
- Session-based authentication

## Technology Stack 🛠️

### Backend
- **Language**: PHP 8.1+
- **Database**: MySQL/MariaDB
- **Architecture**: REST API
- **Pattern**: MVC

### Frontend
- Vanilla JavaScript (ES6+)
- Semantic HTML5
- Custom CSS with hand-drawn styling
- Ably.io for real-time features
- Fetch API for AJAX requests

### Infrastructure
- Apache Web Server
- .htaccess URL routing
- File-based document storage

## Installation Guide 📥

### Prerequisites
- PHP 8.1+
- MySQL/MariaDB
- Apache/Nginx

### Setup Instructions

#### Option 1: Command Line
```bash
# Clone repository
git clone https://github.com/yourusername/wyzmo.git
cd wyzmo

# Create database
mysql -u root -p -e "CREATE DATABASE project"

# Configure database connection
# Edit PFM/Model/model.php with your credentials

# Start development server
php -S localhost:8000 -t PFM/

```
#### Option 2: XAMPP

##### 1. Install XAMPP
Download and install from [apachefriends.org](https://www.apachefriends.org/)

##### Configure Project Root
```bash
# Clone repository to XAMPP's htdocs
git clone https://github.com/yourusername/wyzmo.git C:/xampp/htdocs/wyzmo
```
#### Modify Apache Configuration
```bash
#Open 
\xampp\apache\conf\httpd.conf

#Change these lines
DocumentRoot "C:/xampp/htdocs/wyzmo/wyzmo"
<Directory "C:/xampp/htdocs/wyzmo/wyzmo">

#Restart Apache
```
