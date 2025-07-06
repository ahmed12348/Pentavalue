<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Real-time Sales Analytics</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://js.pusher.com/8.2.0/pusher.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
        }
        
        .header {
            text-align: center;
            color: white;
            margin-bottom: 30px;
        }
        
        .header h1 {
            font-size: 2.5rem;
            margin-bottom: 10px;
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .stat-card {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            text-align: center;
        }
        
        .stat-card h3 {
            color: #666;
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 10px;
        }
        
        .stat-card .value {
            font-size: 2rem;
            font-weight: bold;
            color: #333;
        }
        
        .charts-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .chart-container {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        
        .chart-container h3 {
            margin-bottom: 15px;
            color: #333;
        }
        
        .recommendations {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        
        .recommendations h3 {
            margin-bottom: 15px;
            color: #333;
        }
        
        .recommendation-item {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 10px;
            border-left: 4px solid #667eea;
        }
        
        .weather-info {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 15px;
            padding: 10px;
            background: #e3f2fd;
            border-radius: 5px;
        }
        
        .recent-orders {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            margin-top: 20px;
        }
        
        .order-item {
            display: flex;
            justify-content: space-between;
            padding: 10px;
            border-bottom: 1px solid #eee;
        }
        
        .order-item:last-child {
            border-bottom: none;
        }
        
        .pulse {
            animation: pulse 2s infinite;
        }
        
        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.05); }
            100% { transform: scale(1); }
        }
        
        @media (max-width: 768px) {
            .charts-grid {
                grid-template-columns: 1fr;
            }
            
            .stats-grid {
                grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>📊 Real-time Sales Analytics</h1>
            <p>Live dashboard for monitoring sales performance</p>
        </div>
        
        <div class="stats-grid">
            <div class="stat-card">
                <h3>Total Revenue</h3>
                <div class="value" id="totalRevenue">$0</div>
            </div>
            <div class="stat-card">
                <h3>Revenue (Last Minute)</h3>
                <div class="value" id="revenueLastMinute">$0</div>
            </div>
            <div class="stat-card">
                <h3>Orders (Last Minute)</h3>
                <div class="value" id="ordersLastMinute">0</div>
            </div>
            <div class="stat-card">
                <h3>Current Temperature</h3>
                <div class="value" id="currentTemp">--°C</div>
            </div>
        </div>
        
        <div class="charts-grid">
            <div class="chart-container">
                <h3>Top Products</h3>
                <canvas id="topProductsChart"></canvas>
            </div>
            <div class="chart-container">
                <h3>Revenue Trend</h3>
                <canvas id="revenueChart"></canvas>
            </div>
        </div>
        
        <div class="recommendations">
            <h3>🤖 AI Recommendations</h3>
            <div class="weather-info">
                <span>🌤️ Weather-based insights:</span>
                <span id="weatherInfo">Loading...</span>
            </div>
            <div id="recommendationsList">
                <div class="recommendation-item">Loading recommendations...</div>
            </div>
        </div>
        
        <div class="recent-orders">
            <h3>🆕 Recent Orders</h3>
            <div id="recentOrdersList">
                <div class="order-item">Loading recent orders...</div>
            </div>
        </div>
    </div>

    <script>
        // Initialize Pusher for real-time updates
        const pusher = new Pusher('{{ env("PUSHER_APP_KEY") }}', {
            cluster: '{{ env("PUSHER_APP_CLUSTER", "mt1") }}',
            wsHost: '{{ env("PUSHER_HOST", "127.0.0.1") }}',
            wsPort: {{ env("PUSHER_PORT", 6001) }},
            forceTLS: false,
            enabledTransports: ['ws', 'wss']
        });

        // Subscribe to channels
        const ordersChannel = pusher.subscribe('orders');
        const analyticsChannel = pusher.subscribe('analytics');

        // Charts
        let topProductsChart, revenueChart;
        let revenueData = [];
        let revenueLabels = [];

        // Initialize charts
        function initCharts() {
            const topProductsCtx = document.getElementById('topProductsChart').getContext('2d');
            topProductsChart = new Chart(topProductsCtx, {
                type: 'doughnut',
                data: {
                    labels: [],
                    datasets: [{
                        data: [],
                        backgroundColor: [
                            '#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0', '#9966FF'
                        ]
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'bottom'
                        }
                    }
                }
            });

            const revenueCtx = document.getElementById('revenueChart').getContext('2d');
            revenueChart = new Chart(revenueCtx, {
                type: 'line',
                data: {
                    labels: revenueLabels,
                    datasets: [{
                        label: 'Revenue',
                        data: revenueData,
                        borderColor: '#667eea',
                        backgroundColor: 'rgba(102, 126, 234, 0.1)',
                        tension: 0.4
                    }]
                },
                options: {
                    responsive: true,
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });
        }

        // Load initial data
        async function loadInitialData() {
            try {
                const [analyticsResponse, recommendationsResponse, recentOrdersResponse] = await Promise.all([
                    axios.get('/api/analytics'),
                    axios.get('/api/recommendations'),
                    axios.get('/api/analytics/recent-orders')
                ]);

                updateAnalytics(analyticsResponse.data);
                updateRecommendations(recommendationsResponse.data);
                updateRecentOrders(recentOrdersResponse.data);
            } catch (error) {
                console.error('Error loading initial data:', error);
            }
        }

        // Update analytics display
        function updateAnalytics(data) {
            document.getElementById('totalRevenue').textContent = `$${parseFloat(data.total_revenue).toFixed(2)}`;
            document.getElementById('revenueLastMinute').textContent = `$${parseFloat(data.revenue_last_minute).toFixed(2)}`;
            document.getElementById('ordersLastMinute').textContent = data.orders_last_minute;
            
            // Update top products chart
            if (data.top_products && data.top_products.length > 0) {
                topProductsChart.data.labels = data.top_products.map(p => `Product ${p.product_id}`);
                topProductsChart.data.datasets[0].data = data.top_products.map(p => p.total_sold);
                topProductsChart.update();
            }

            // Update revenue trend
            const now = new Date().toLocaleTimeString();
            revenueLabels.push(now);
            revenueData.push(parseFloat(data.revenue_last_minute));
            
            if (revenueLabels.length > 10) {
                revenueLabels.shift();
                revenueData.shift();
            }
            
            revenueChart.update();
        }

        // Update recommendations
        function updateRecommendations(data) {
            if (data.weather) {
                document.getElementById('currentTemp').textContent = `${data.weather}°C`;
            }
            
            if (data.suggestion) {
                document.getElementById('recommendationsList').innerHTML = `
                    <div class="recommendation-item">
                        <strong>AI Suggestion:</strong> ${data.suggestion}
                    </div>
                `;
            }
        }

        // Update recent orders
        function updateRecentOrders(orders) {
            const ordersList = document.getElementById('recentOrdersList');
            if (orders.length === 0) {
                ordersList.innerHTML = '<div class="order-item">No orders yet</div>';
                return;
            }
            
            ordersList.innerHTML = orders.map(order => `
                <div class="order-item">
                    <div>
                        <strong>Product ${order.product_id}</strong> - Qty: ${order.quantity}
                        <br><small>${new Date(order.date).toLocaleString()}</small>
                    </div>
                    <div>$${(order.price * order.quantity).toFixed(2)}</div>
                </div>
            `).join('');
        }

        // Handle new order
        function handleNewOrder(order) {
            const ordersList = document.getElementById('recentOrdersList');
            const orderElement = document.createElement('div');
            orderElement.className = 'order-item pulse';
            orderElement.innerHTML = `
                <div>
                    <strong>Product ${order.product_id}</strong> - Qty: ${order.quantity}
                </div>
                <div>$${(order.price * order.quantity).toFixed(2)}</div>
            `;
            
            ordersList.insertBefore(orderElement, ordersList.firstChild);
            
            // Remove old orders if more than 10
            while (ordersList.children.length > 10) {
                ordersList.removeChild(ordersList.lastChild);
            }
            
            // Remove pulse animation after 2 seconds
            setTimeout(() => {
                orderElement.classList.remove('pulse');
            }, 2000);
        }

        // Listen for real-time events
        ordersChannel.bind('order.placed', function(data) {
            handleNewOrder(data.order);
            loadInitialData(); // Refresh analytics
        });

        analyticsChannel.bind('order.placed', function(data) {
            loadInitialData(); // Refresh analytics
        });

        // Initialize everything
        document.addEventListener('DOMContentLoaded', function() {
            initCharts();
            loadInitialData();
            
            // Refresh data every 30 seconds
            setInterval(loadInitialData, 30000);
        });
    </script>
</body>
</html> 