// Sa isang totoong IoT setup, papalitan ang script na ito ng isang microcontroller 
// (tulad ng Arduino o ESP32) na may ultrasonic sensor para sukatin ang tubig, 
// at magpapadala ng data gamit ang Wi-Fi o MQTT.

const API_URL = "http://localhost:8000/api/readings"; // Palitan ito ng live URL kapag na-deploy na
let currentWaterLevel = 80.0; // Magsisimula sa 80 cm

setInterval(async () => {
    // Baguhin ang water level nang unti-unti (-2 hanggang +2 cm lang para hindi random tumalon)
    const change = (Math.random() * 4) - 2;
    currentWaterLevel += change;
    
    // Siguraduhing hindi bababa sa 0 at hindi lalampas sa 500
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
}, 5000); // Tumatakbo tuwing 5 segundo
