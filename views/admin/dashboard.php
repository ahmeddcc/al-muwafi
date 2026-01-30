<?php
/**
 * لوحة التحكم الرئيسية - التصميم العصري
 */

$currentPage = 'dashboard';
ob_start();
?>

<!-- قسم الترحيب -->


<!-- رأس الصفحة -->

<!-- شبكة الإحصائيات -->
<div class="stats-grid">
    <!-- إجمالي التذاكر -->
    <div class="glass-card stat-card-modern card-primary">
        <div class="stat-content">
            <h3 style="color: #fff;"><?= $ticketStats['total'] ?></h3>
            <p style="color: #94a3b8;">إجمالي التذاكر</p>
        </div>
        <div class="stat-icon-wrapper">
            <img src="https://img.icons8.com/fluency/96/ticket.png" class="stat-icon-img" alt="Tickets">
        </div>
    </div>
    
    <!-- التذاكر الجديدة -->
    <div class="glass-card stat-card-modern card-info">
        <div class="stat-content">
            <h3 style="color: #fff;"><?= $ticketStats['new'] ?></h3>
            <p style="color: #94a3b8;">تذاكر جديدة</p>
        </div>
        <div class="stat-icon-wrapper">
            <img src="https://img.icons8.com/fluency/96/add-ticket.png" class="stat-icon-img" alt="New">
        </div>
    </div>
    
    <!-- قيد العمل -->
    <div class="glass-card stat-card-modern card-warning">
        <div class="stat-content">
            <h3 style="color: #fff;"><?= $ticketStats['in_progress'] ?></h3>
            <p style="color: #94a3b8;">قيد العمل</p>
        </div>
        <div class="stat-icon-wrapper">
            <img src="https://img.icons8.com/fluency/96/maintenance.png" class="stat-icon-img" alt="Work">
        </div>
    </div>
    
    <!-- رسائل غير مقروءة (جديد) -->
    <div class="glass-card stat-card-modern card-danger">
        <div class="stat-content">
            <h3 class="text-danger-glow"><?= $unreadMessages ?></h3>
            <p class="text-danger-soft">رسائل جديدة</p>
        </div>
        <div class="stat-icon-wrapper">
            <img src="https://img.icons8.com/fluency/96/chat-message.png" class="stat-icon-img" alt="Messages">
        </div>
    </div>
</div>

<!-- صف الأعطال المتكررة (جديد) -->
<div class="glass-card" style="margin-bottom: 2rem;">
    <div class="section-header">
        <div class="section-title">
            <img src="https://img.icons8.com/fluency/48/high-priority.png" class="section-icon" alt="Warning">
            الأعطال الأكثر تكراراً
        </div>
    </div>
    <div class="table-responsive">
        <table class="modern-table">
            <thead>
                <tr>
                    <th>موديل الجهاز</th>
                    <th>وصف العطل</th>
                    <th>عدد التكرار</th>
                    <th>الإجراء المقترح</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($repeatedFaults)): ?>
                <tr><td colspan="4" style="text-align:center; color:#94a3b8;">لا توجد بيانات للأعطال المتكررة حالياً</td></tr>
                <?php else: ?>
                <?php foreach ($repeatedFaults as $fault): ?>
                <tr>
                    <td><span style="color: #cbd5e1; font-weight:bold;"><?= htmlspecialchars($fault['machine_model']) ?></span></td>
                    <td><?= htmlspecialchars($fault['fault_description']) ?></td>
                    <td><span class="glass-badge badge-danger-soft"><?= $fault['count'] ?> مرة</span></td>
                    <td><a href="#" class="link-primary">عرض الحلول السابقة</a></td>
                </tr>
                <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- قسم الرسوم البيانية والجداول -->
<div style="display: grid; grid-template-columns: 2fr 1fr; gap: 1.5rem; margin-bottom: 2rem;">
    
    <!-- الرسم البياني للأداء (مكان التذاكر الأخيرة سابقاً) -->
    <div class="glass-card">
        <div class="section-header">
            <div class="section-title">
                <img src="https://img.icons8.com/fluency/48/graph.png" class="section-icon" alt="Graph">
                تحليل التذاكر
            </div>
            <select style="border:none; background:rgba(0,0,0,0.05); padding:5px 10px; border-radius:8px; color: #cbd5e1;">
                <option>آخر 7 أيام</option>
            </select>
        </div>
        <!-- حاوية الرسم البياني -->
        <div id="ticketsChart" style="height: 300px;"></div>
    </div>

    <!-- الرسم الدائري للحالة -->
    <div class="glass-card">
        <div class="section-header">
            <div class="section-title">
                <img src="https://img.icons8.com/fluency/48/pie-chart.png" class="section-icon" alt="Pie">
                توزيع الحالات
            </div>
        </div>
        <div id="statusChart" style="height: 300px; display: flex; align-items: center; justify-content: center;"></div>
    </div>
</div>

<!-- جدول آخر التذاكر (بتصميم حديث) -->
<div class="glass-card">
    <div class="section-header">
        <div class="section-title">
            <img src="https://img.icons8.com/fluency/48/list.png" class="section-icon" alt="List">
            أحدث التذاكر المستلمة
        </div>
        <a href="<?= BASE_URL ?>/admin/tickets" class="btn-glass-primary">
            عرض الكل <span style="font-size:1.2em">→</span>
        </a>
    </div>
    
    <div class="table-responsive">
        <table class="modern-table">
            <thead>
                <tr>
                    <th>رقم التذكرة</th>
                    <th>العميل</th>
                    <th>نوع الجهاز</th>
                    <th>الحالة</th>
                    <th>التاريخ</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($recentTickets)): ?>
                <tr>
                    <td colspan="5" style="text-align: center; color: #94a3b8; padding: 2rem;">لا توجد تذاكر حالياً 📭</td>
                </tr>
                <?php else: ?>
                <?php foreach ($recentTickets as $ticket): ?>
                <tr>
                    <td>
                        <span style="font-family: monospace; font-weight: bold; color: #3b82f6;">#<?= htmlspecialchars($ticket['ticket_number']) ?></span>
                    </td>
                    <td>
                        <div style="display: flex; align-items: center; gap: 0.5rem;">
                            <img src="https://img.icons8.com/fluency/48/user-male-circle.png" width="32" alt="User">
                            <?= htmlspecialchars($ticket['customer_name']) ?>
                        </div>
                    </td>
                    <td>
                        <div style="display: flex; align-items: center; gap: 0.5rem;">
                             <img src="https://img.icons8.com/fluency/48/print.png" width="24" alt="Printer">
                             <?= $ticket['machine_type'] === 'copier' ? 'آلة تصوير' : 'طابعة' ?>
                        </div>
                    </td>
                    <td>
                        <?php
                        $statusClass = match($ticket['status']) {
                            'new' => 'badge-new',
                            'in_progress', 'received', 'under_review' => 'badge-working',
                            'fixed', 'closed', 'delivered' => 'badge-closed',
                            default => 'badge-new'
                        };
                        $statusText = [
                            'new' => 'جديدة', 'received' => 'مستلمة', 'under_review' => 'فحص', 
                            'in_progress' => 'قيد العمل', 'fixed' => 'تم الإصلاح', 
                            'delivered' => 'تم التسليم', 'closed' => 'مغلقة'
                        ];
                        ?>
                        <span class="glass-badge <?= $statusClass ?>">
                            <?= $statusText[$ticket['status']] ?? $ticket['status'] ?>
                        </span>
                    </td>
                    <td style="font-size: 0.85rem; color: #94a3b8;">
                        <?= date('Y-m-d', strtotime($ticket['created_at'])) ?>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    // 1. رسم بياني خطي (Tickets Trend) - بيانات حقيقية
    var optionsLine = {
        series: [{
            name: 'تذاكر جديدة',
            data: <?= json_encode($chartData['new']) ?>
        }, {
            name: 'تم إنجازها',
            data: <?= json_encode($chartData['closed']) ?>
        }],
        chart: {
            height: 300,
            type: 'area',
            fontFamily: 'Cairo, sans-serif',
            background: 'transparent',
            toolbar: { show: false }
        },
        theme: {
            mode: 'dark', 
            palette: 'palette1'
        },
        grid: {
            borderColor: '#334155',
            strokeDashArray: 4,
        },
        colors: ['#3b82f6', '#10b981'],
        dataLabels: { enabled: false },
        stroke: { curve: 'smooth', width: 2 },
        fill: { type: 'gradient', gradient: { shadeIntensity: 1, opacityFrom: 0.7, opacityTo: 0.2, stops: [0, 90, 100] } },
        xaxis: {
            categories: <?= json_encode($chartData['dates']) ?>,
            labels: {
                style: { colors: '#94a3b8', fontFamily: 'Cairo, sans-serif' }
            },
            axisBorder: { show: false },
            axisTicks: { show: false }
        },
        yaxis: {
            labels: {
                style: { colors: '#94a3b8', fontFamily: 'Cairo, sans-serif' }
            }
        },
        tooltip: {
            theme: 'dark',
            style: { fontSize: '14px', fontFamily: 'Cairo, sans-serif' },
            x: { show: true }
        }
    };

    var chartLine = new ApexCharts(document.querySelector("#ticketsChart"), optionsLine);
    chartLine.render();

    // 2. رسم حلقي (Status Donut)
    var optionsDonut = {
        series: [<?= $ticketStats['new'] ?>, <?= $ticketStats['in_progress'] ?>, <?= $ticketStats['closed'] ?>],
        labels: ['جديدة', 'قيد العمل', 'مغلقة'],
        chart: {
            type: 'donut',
            height: 300,
            fontFamily: 'Cairo, sans-serif',
            background: 'transparent'
        },
        theme: {
            mode: 'dark', 
            palette: 'palette1'
        },
        stroke: {
            show: true,
            colors: ['transparent']
        },
        colors: ['#3b82f6', '#f59e0b', '#10b981'],
        plotOptions: {
            pie: {
                donut: {
                    size: '65%',
                    labels: {
                        show: true,
                        name: {
                            color: '#94a3b8',
                            fontFamily: 'Cairo, sans-serif'
                        },
                        value: {
                            color: '#fff',
                            fontFamily: 'Cairo, sans-serif',
                            fontWeight: 700
                        },
                        total: {
                            show: true,
                            label: 'المجموع',
                            color: '#e2e8f0',
                            fontFamily: 'Cairo, sans-serif',
                            formatter: function (w) {
                                return w.globals.seriesTotals.reduce((a, b) => a + b, 0)
                            }
                        }
                    }
                }
            }
        },
        legend: { 
            position: 'bottom',
            labels: { colors: '#cbd5e1', fontFamily: 'Cairo, sans-serif' }
        },
        dataLabels: { enabled: false }
    };

    var chartDonut = new ApexCharts(document.querySelector("#statusChart"), optionsDonut);
    chartDonut.render();
});
</script>


<!-- نافذة الترحيب (Welcome Modal) -->
<?php if (isset($_SESSION['show_welcome_modal']) && $_SESSION['show_welcome_modal']): ?>
    <div id="welcomeModal" class="welcome-modal-overlay">
        <div class="welcome-modal-content">
            <div class="welcome-modal-body">
                <div class="welcome-icon-large">
                    <i class="fas fa-tools"></i>
                </div>
                <div class="welcome-message-container">
                    <?php 
                    $welcomeMessages = [
                        "نتمنى لك يوماً مليئاً بالإنجازات والنجاح.",
                        "جاهزون لدعمك في إدارة مهام الصيانة بكل كفاءة.",
                        "إبداعك اليوم يبدأ بترتيب أولوياتك هنا.",
                        "كل يوم هو فرصة جديدة لتحقيق التميز.",
                        "دعنا نجعل سير العمل اليوم أكثر سلاسة وتنظيماً.",
                        "النجاح هو ومحصلة اجتهادات صغيرة تتراكم يوماً بعد يوم.",
                        "بداية يوم جديد تعني بداية تحديات وإنجازات جديدة.",
                        "نحن هنا لنجعل إدارة عملك أسهل وأكثر متعة.",
                        "ثقتك بنا هي دافعنا لتقديم الأفضل دائماً.",
                        "لا تؤجل عمل اليوم إلى الغد، ابدأ الآن!",
                        "التنظيم هو سر النجاح، ولوحة التحكم هي أداتك.",
                        "كل تذكرة تغلقها هي خطوة نحو رضا عملائك.",
                        "استثمر وقتك بحكمة، فالوقت هو أثمن الموارد.",
                        "الجودة تعني أن تفعل ذلك بشكل صحيح عندما لا ينظر أحد.",
                        "التميز ليس عملاً، بل هو عادة.",
                        "فريق العمل الناجح يبدأ بإدارة ناجحة.",
                        "اجعل من كل عقبة فرصة للتعلم والتطوير.",
                        "القيادة هي القدرة على ترجمة الرؤية إلى واقع.",
                        "ركز على الحلول، وليس على المشاكل.",
                        "الابتكار يميز بين القائد والتابع.",
                        "سر النجاح هو الثبات على الهدف.",
                        "الفرص لا تحدث، أنت من يصنعها.",
                        "العمل الجاد يتغلب على الموهبة عندما لا تعمل الموهبة بجد.",
                        "كن التغيير الذي تريد أن تراه في العالم.",
                        "أفضل طريقة للتنبؤ بالمستقبل هي ابتكاره.",
                        "لا تتوقف عندما تتعب، توقف عندما تنتهي.",
                        "النجاح لا يأتي إليك، عليك أن تذهب إليه.",
                        "الطريق إلى النجاح دائماً تحت الإنشاء.",
                        "كل إنجاز عظيم كان يعتبر مستحيلاً في البداية.",
                        "لا تحلم بالنجاح، بل استيقظ واعمل لتحقيقه.",
                        "اليوم هو يوم رائع لتحقيق أهدافك."
                    ];
                    $randomMessage = $welcomeMessages[array_rand($welcomeMessages)];
                    ?>
                    <h2 class="welcome-title">مرحباً، مهندس <?= htmlspecialchars($user['full_name'] ?? 'مدير النظام') ?> <span class="wave-emoji">👋</span></h2>
                    <p class="welcome-subtitle"><?= $randomMessage ?></p>
                </div>
                <!-- زخرفة إضافية -->
                <div class="welcome-shine"></div>
            </div>
        </div>
    </div>
    
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const modal = document.getElementById('welcomeModal');
        
        // تشغيل الصوت (اختياري)
        // const audio = new Audio('<?= BASE_URL ?>/assets/sounds/welcome.mp3');
        // audio.play().catch(e => console.log('Audio autoplay blocked'));
        
        // إظهار النافذة
        setTimeout(() => {
            modal.classList.add('show');
        }, 500);
        
        // إخفاء النافذة تلقائياً بعد 10 ثواني (كما طلب المستخدم)
        setTimeout(() => {
            modal.classList.remove('show');
            setTimeout(() => {
                modal.remove();
            }, 500);
        }, 10000);
    });
    </script>
    <?php unset($_SESSION['show_welcome_modal']); ?>
<?php endif; ?>

<?php
$content = ob_get_clean();
include VIEWS_PATH . '/layouts/admin_layout.php';
?>
