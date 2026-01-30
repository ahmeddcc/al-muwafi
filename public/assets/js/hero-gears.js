
document.addEventListener('DOMContentLoaded', function () {
    const canvas = document.getElementById('heroGearsCanvas');
    if (!canvas) return;

    const ctx = canvas.getContext('2d');
    let width, height;

    function resize() {
        width = canvas.width = canvas.parentElement.offsetWidth;
        height = canvas.height = canvas.parentElement.offsetHeight;
    }
    window.addEventListener('resize', resize);
    resize();

    // Gear Class
    class Gear {
        constructor(x, y, radius, teeth, speed, color, blur) {
            this.x = x;
            this.y = y;
            this.radius = radius;
            this.teeth = teeth;
            this.speed = speed;
            this.angle = 0;
            this.color = color;
            this.blur = blur;
        }

        draw() {
            ctx.save();
            ctx.translate(this.x, this.y);
            ctx.rotate(this.angle);

            // Foggy/Glow Effect
            ctx.shadowBlur = this.blur;
            ctx.shadowColor = this.color;
            ctx.fillStyle = this.color;

            // Draw Gear Shape
            ctx.beginPath();
            const outerRadius = this.radius;
            const innerRadius = this.radius * 0.85;
            const holeRadius = this.radius * 0.3;
            const teethHeight = this.radius * 0.15;

            for (let i = 0; i < this.teeth * 2; i++) {
                const a = (Math.PI * 2 * i) / (this.teeth * 2);
                const r = (i % 2 === 0) ? outerRadius + teethHeight : innerRadius;
                ctx.lineTo(Math.cos(a) * r, Math.sin(a) * r);
            }
            ctx.closePath();
            ctx.fill();

            // Center Hole (Clear)
            ctx.globalCompositeOperation = 'destination-out';
            ctx.beginPath();
            ctx.arc(0, 0, holeRadius, 0, Math.PI * 2);
            ctx.fill();

            // Restore standard composite
            ctx.globalCompositeOperation = 'source-over';
            ctx.restore();
        }

        update() {
            this.angle += this.speed;
        }
    }

    // Create Gears - Strategic positioning for visual balance
    const gears = [
        // Large faint background gear
        new Gear(width * 0.8, height * 0.5, 300, 24, 0.0005, 'rgba(212, 175, 55, 0.05)', 20),
        // Primary detail gear (Gold)
        new Gear(width * 0.2, height * 0.8, 180, 16, -0.001, 'rgba(212, 175, 55, 0.1)', 30),
        // Small connector (Brighter)
        new Gear(width * 0.25, height * 0.2, 80, 12, 0.002, 'rgba(255, 215, 0, 0.15)', 15)
    ];

    function animate() {
        ctx.clearRect(0, 0, width, height);

        // Dark Fog Overlay (Gradient)
        const gradient = ctx.createLinearGradient(0, 0, 0, height);
        gradient.addColorStop(0, 'rgba(2, 6, 23, 0.2)');
        gradient.addColorStop(1, 'rgba(2, 6, 23, 0.8)');
        ctx.fillStyle = gradient;
        ctx.fillRect(0, 0, width, height);

        gears.forEach(gear => {
            // Update position relative to screen size (responsive centering)
            if (gear.radius > 200) { gear.x = width * 0.85; gear.y = height * 0.6; } // Bind large gear

            gear.update();
            gear.draw();
        });

        requestAnimationFrame(animate);
    }

    animate();
});
