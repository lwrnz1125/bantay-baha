// Sa isang totoong IoT setup, papalitan ang script na ito ng isang microcontroller 
// (tulad ng Arduino o ESP32) na may ultrasonic sensor para sukatin ang tubig, 
// at magpapadala ng data gamit ang Wi-Fi o MQTT.

// Pinalitan ng Live URL:
const API_URL = "http://bantay-baha-monitor.wuaze.com/api.php/api/readings"; 
let currentWaterLevel = 80.0; 

setInterval(async () => {
    const change = (Math.random() * 4) - 2;
    currentWaterLevel += change;
    
    if (currentWaterLevel < 0) currentWaterLevel = 0;
    if (currentWaterLevel > 500) currentWaterLevel = 500;

    const reading = {
        sensorId: "CREEK-01",
        waterLevelCm: parseFloat(currentWaterLevel.toFixed(1)),
        timestamp: new Date().toISOString()
    };

    try {
        const response = await fetch(API_URL, {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify(reading)
        });
        console.log(`Nagpadala ng reading: ${reading.waterLevelCm} cm - Status: ${response.status}`);
    } catch (error) {
        console.error("Hindi makakonekta sa API:", error.message);
    }
}, 5000);
