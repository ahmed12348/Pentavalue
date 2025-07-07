# 📊 Real-time Sales Analytics Dashboard

A modern, real-time sales analytics application built with Laravel, featuring live WebSocket updates, AI-powered recommendations, and beautiful data visualizations.

## 🚀 Features

### ✅ **Real-time Dashboard**
- Live sales data updates via WebSocket
- Beautiful, responsive UI with Chart.js visualizations
- Real-time order notifications with animations
- Auto-refreshing analytics every 30 seconds

### ✅ **Analytics & Reporting**
- Total revenue tracking
- Revenue per minute monitoring
- Top products analysis
- Recent orders display
- Revenue trend charts

### ✅ **AI-Powered Insights**
- OpenAI integration for intelligent recommendations
- Weather-based product suggestions
- Sales pattern analysis
- Automated marketing recommendations

### ✅ **Technical Features**
- SQLite database (easy setup, no server required)
- Raw SQL queries (no ORM dependency)
- RESTful API endpoints
- WebSocket real-time communication
- Modern Laravel 10 architecture

## 🛠️ Technology Stack

- **Backend**: Laravel 10, PHP 8.1+
- **Database**: SQLite
- **Real-time**: Laravel WebSockets (Pusher alternative)
- **Frontend**: HTML5, CSS3, JavaScript, Chart.js
- **APIs**: OpenAI GPT-3.5, OpenWeather API
- **Charts**: Chart.js for data visualization

## 📋 Prerequisites

- PHP 8.1 or higher
- Composer
- Node.js & NPM (for frontend assets)
- Git

## 🚀 Installation & Setup

### 1. **Clone the Repository**
```bash
git clone https://github.com/ahmed12348/Pentavalue
cd Pentavalue
```

### 2. **Install Dependencies**
```bash
composer install
npm install
```

### 3. **Environment Configuration**
Copy the environment configuration:
```bash
cp env_config.txt .env
```

Update your `.env` file with the following settings:
```env
APP_NAME="Real-time Sales Analytics"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost:8000

# Database Configuration (SQLite)
DB_CONNECTION=sqlite
DB_DATABASE=database/database.sqlite

# Broadcasting Configuration
BROADCAST_DRIVER=pusher
PUSHER_APP_ID=local-app
PUSHER_APP_KEY=localkey123
PUSHER_APP_SECRET=localsecret123
PUSHER_HOST=127.0.0.1
PUSHER_PORT=6001
PUSHER_SCHEME=http
PUSHER_APP_CLUSTER=mt1

# Frontend Variables
VITE_PUSHER_APP_KEY="${PUSHER_APP_KEY}"
VITE_PUSHER_HOST="${PUSHER_HOST}"
VITE_PUSHER_PORT="${PUSHER_PORT}"
VITE_PUSHER_SCHEME="${PUSHER_SCHEME}"
VITE_PUSHER_APP_CLUSTER="${PUSHER_APP_CLUSTER}"

# API Keys (Optional - for AI recommendations)
OPENAI_API_KEY=your_openai_api_key_here
OPENWEATHER_API_KEY=your_openweather_api_key_here
```

### 4. **Generate Application Key**
```bash
php artisan key:generate
```

### 5. **Setup Database**
```bash
# Create SQLite database file
touch database/database.sqlite

# Run migrations
php artisan migrate

# Add sample data (optional)
php artisan tinker --execute="DB::table('orders')->insert(['product_id' => 1, 'quantity' => 5, 'price' => 29.99, 'date' => now(), 'created_at' => now(), 'updated_at' => now()]); DB::table('orders')->insert(['product_id' => 2, 'quantity' => 3, 'price' => 19.99, 'date' => now(), 'created_at' => now(), 'updated_at' => now()]); echo 'Sample data inserted!';"
```

### 6. **Start the Servers**

**Terminal 1 - Laravel Server:**
```bash
php artisan serve
```

**Terminal 2 - WebSocket Server:**
```bash
php artisan websockets:serve
```

**Terminal 3 - Frontend Assets (Optional):**
```bash
npm run dev
```

## 🌐 Access the Application

- **Main Dashboard**: http://localhost:8000
- **WebSocket Dashboard**: http://localhost:8000/laravel-websockets
- **API Documentation**: See API Endpoints section below

## 📡 API Endpoints

### **Orders**
```http
POST /api/orders
Content-Type: application/json

{
    "product_id": 1,
    "quantity": 5,
    "price": 29.99,
    "date": "2024-01-15 10:30:00"
}
```

### **Analytics**
```http
GET /api/analytics
```
Returns: Total revenue, top products, revenue per minute, orders per minute

### **Recent Orders**
```http
GET /api/analytics/recent-orders
```
Returns: Last 10 orders with details

### **AI Recommendations**
```http
GET /api/recommendations
```
Returns: AI-powered suggestions based on sales data and weather

## 🧪 Testing the Application



### **2. Test Real-time Updates**
1. Open the dashboard in multiple browser tabs
2. Create orders via API
3. Watch real-time updates across all tabs

### **3. View WebSocket Dashboard**
Visit http://localhost:8000/laravel-websockets to see:
- Active connections
- Channel subscriptions
- Real-time statistics

## 🎯 Key Features Explained

### **Real-time Updates**
- Uses Laravel WebSockets (Pusher alternative)
- Events broadcast on 'orders' and 'analytics' channels
- Frontend automatically updates when new orders are created

### **AI Recommendations**
- Fetches recent sales data (last 24 hours)
- Calls OpenAI API for intelligent suggestions
- Integrates weather data for contextual recommendations
- Falls back gracefully if API keys are not configured

### **Database Design**
- Uses raw SQL queries (no Eloquent ORM)
- SQLite for easy development setup
- Optimized for real-time analytics

### **Frontend Features**
- Responsive design that works on all devices
- Chart.js for beautiful data visualizations
- Real-time animations for new orders
- Auto-refresh functionality

## 🔧 Configuration Options

### **Customizing the Dashboard**
- Modify `resources/views/dashboard.blade.php` for UI changes
- Update chart configurations in the JavaScript section
- Customize colors and styling in the CSS section

### **Adding New Analytics**
- Extend `AnalyticsController` for new metrics
- Add new API endpoints in `routes/api.php`
- Update frontend JavaScript to display new data

### **WebSocket Configuration**
- Modify `config/websockets.php` for advanced settings
- Update `config/broadcasting.php` for Pusher configuration
- Customize event broadcasting in `app/Events/OrderPlaced.php`

## 🐛 Troubleshooting

### **WebSocket Connection Issues**
1. Ensure WebSocket server is running: `php artisan websockets:serve`
2. Check port 6001 is not blocked by firewall
3. Verify App ID matches in `.env` and `config/websockets.php`
4. Clear config cache: `php artisan config:clear`

### **API Errors**
1. Check database connection and migrations
2. Verify API keys are properly configured
3. Check Laravel logs: `storage/logs/laravel.log`

### **Frontend Issues**
1. Ensure all JavaScript libraries are loaded
2. Check browser console for errors
3. Verify WebSocket connection in Network tab

## 📊 Sample Data

The application includes sample data to demonstrate functionality:
- Product sales across multiple products
- Revenue tracking over time
- Order history for analytics

## 🔮 Future Enhancements

- [ ] User authentication and roles
- [ ] Advanced filtering and date ranges
- [ ] Export functionality (PDF, Excel)
- [ ] Email notifications
- [ ] Mobile app integration
- [ ] Advanced AI analytics
- [ ] Multi-store support

## 📝 License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

## 🤝 Contributing

1. Fork the repository
2. Create a feature branch
3. Make your changes
4. Add tests if applicable
5. Submit a pull request

## 📞 Support

For support and questions:
- Check the troubleshooting section above
- Review Laravel documentation
- Open an issue on GitHub

## 📬 Postman Collection

You can test all API endpoints using the provided Postman collection.

**How to use:**
1. Download the collection file: [`postman/RealTimeSalesAnalytics.postman_collection.json`](postman/RealTimeSalesAnalytics.postman_collection.json)
2. Open Postman and click `Import`.
3. Select the downloaded JSON file.
4. Use the pre-configured requests to test all API endpoints (orders, analytics, recommendations, etc).

---

**Built with ❤️ using Laravel and modern web technologies**
