@include('includes.header')

<x-menu />

<section class="emicalc_sec">
    <div class="container-lg">
        <div class="sec_title">
            <h1 class="title21">EMI Calculator</h1>
        </div>
        <div class="emicalc_grid">
            <div class="emiform_area">
                <div class="emi_victor">
                    <img src="{{ asset('frontend-assets/images/victor-dash10.svg') }}" alt="victor"
                        class="img-fluid w-100">
                </div>
                <div class="emi_form">
                    <h3 class="title48">Emi Calculator</h3>
                    <div class="emiform_wrap">
                        <div class="form-group">
                            <label for="">Loan Amount</label>
                            <div class="eminput_grid">
                                <div class="emi_icon">
                                    <img src="{{ asset('frontend-assets/images/rupee-sign.svg') }}" alt="icon"
                                        class="img-fluid">
                                </div>
                                <input type="text" placeholder="25,00,000" class="form-control">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="">Interest Rate</label>
                            <div class="eminput_grid">
                                <div class="emi_icon">
                                    <img src="{{ asset('frontend-assets/images/percentage.svg') }}" alt="icon"
                                        class="img-fluid">
                                </div>
                                <input type="text" placeholder="10.5" class="form-control">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="">Loan Tenure</label>
                            <div class="eminput_grid">
                                <div class="emi_icon">
                                    <img src="{{ asset('frontend-assets/images/clock-seven.svg') }}" alt="icon"
                                        class="img-fluid">
                                </div>
                                <input type="text" placeholder="20" class="form-control">
                                <div class="emiyr_btn">
                                    <button type="button" class="btn active">Yr</button>
                                    <button type="button" class="btn">Mo</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="emiform_info">
                    <div class="emiinfo_item">
                        <p>Loan EMI</p>
                        <h3>₹ 24,959</h3>
                    </div>
                    <div class="emiinfo_item">
                        <p>Total Interest Payable</p>
                        <h3>₹ 34,90,279</h3>
                    </div>
                    <div class="emiinfo_item">
                        <p>Total of Payments (Principal + Interest)</p>
                        <h3>₹ 59,90,279</h3>
                    </div>
                </div>
                <div class="emi_chart">
                    <div class="payment-chart">
                        <h4>Break-up of Total Payment</h4>

                        <div class="chart-area">
                            <canvas id="paymentChart"></canvas>
                        </div>

                        <div class="chart-legend">
                            <div class="legend-item">
                                <span class="legend-color green"></span>
                                <span>Principal Loan Amount</span>
                            </div>

                            <div class="legend-item">
                                <span class="legend-color orange"></span>
                                <span>Total Interest</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</section>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js"></script>
<script>

const loanAmountInput = document.querySelector('.emiform_wrap .form-group:nth-child(1) input');
const interestRateInput = document.querySelector('.emiform_wrap .form-group:nth-child(2) input');
const tenureInput = document.querySelector('.emiform_wrap .form-group:nth-child(3) input');
const yrBtn = document.querySelector('.emiyr_btn .btn:nth-child(1)');
const moBtn = document.querySelector('.emiyr_btn .btn:nth-child(2)');

const emiInfoItems = document.querySelectorAll('.emiform_info .emiinfo_item h3');
const emiEl = emiInfoItems[0];
const totalInterestEl = emiInfoItems[1];
const totalPaymentEl = emiInfoItems[2];

let tenureUnit = 'yr'; // 'yr' or 'mo'

function parseNumber(val) {
    if (!val) return 0;
    const cleaned = val.toString().replace(/,/g, '').trim();
    const num = parseFloat(cleaned);
    return isNaN(num) ? 0 : num;
}

function formatINR(num) {
    return '₹ ' + Math.round(num).toLocaleString('en-IN');
}

// Indian digit grouping for the integer part: 25,00,000
function groupIndian(intStr) {
    if (intStr.length <= 3) return intStr;
    const last3 = intStr.slice(-3);
    const rest = intStr.slice(0, -3);
    const restGrouped = rest.replace(/\B(?=(\d{2})+(?!\d))/g, ',');
    return restGrouped + ',' + last3;
}

// --- Live numeric formatting as user types ---
// allowDecimal = true  -> digits + single '.' (Interest Rate)
// allowDecimal = false -> digits only, grouped Indian-style (Loan Amount, Tenure)
function restrictToNumeric(input, allowDecimal) {
    input.addEventListener('keydown', function (e) {
        const isControlKey = [
            'Backspace', 'Delete', 'Tab', 'Escape', 'Enter',
            'ArrowLeft', 'ArrowRight', 'ArrowUp', 'ArrowDown', 'Home', 'End'
        ].includes(e.key) || e.ctrlKey || e.metaKey;

        if (isControlKey) return;

        const isDigit = /^[0-9]$/.test(e.key);
        const isDecimalPoint = allowDecimal && e.key === '.' && !input.value.includes('.');

        if (!isDigit && !isDecimalPoint) {
            e.preventDefault();
        }
    });

    input.addEventListener('input', function () {
        const cursorAtEnd = input.selectionStart === input.value.length;
        let raw = input.value.replace(/,/g, '');

        if (allowDecimal) {
            raw = raw.replace(/[^0-9.]/g, '');
            const parts = raw.split('.');
            let intPart = parts[0] || '';
            let decPart = parts.length > 1 ? parts.slice(1).join('') : null;

            intPart = groupIndian(intPart);

            input.value = decPart !== null ? intPart + '.' + decPart : intPart;
        } else {
            raw = raw.replace(/[^0-9]/g, '');
            input.value = groupIndian(raw);
        }

        if (cursorAtEnd) {
            input.setSelectionRange(input.value.length, input.value.length);
        }
    });
}

restrictToNumeric(loanAmountInput, false);
restrictToNumeric(interestRateInput, true);
restrictToNumeric(tenureInput, false);
// --- end numeric formatting ---

function calculateEMI() {
    const P = parseNumber(loanAmountInput.value || loanAmountInput.placeholder);
    const annualRate = parseNumber(interestRateInput.value || interestRateInput.placeholder);
    let tenureRaw = parseNumber(tenureInput.value || tenureInput.placeholder);

    const n = tenureUnit === 'yr' ? tenureRaw * 12 : tenureRaw; // months
    const r = annualRate / 12 / 100; // monthly rate

    if (P <= 0 || n <= 0) {
        emiEl.textContent = '₹ 0';
        totalInterestEl.textContent = '₹ 0';
        totalPaymentEl.textContent = '₹ 0';
        updateChart(0, 0);
        return;
    }

    let emi;
    if (r === 0) {
        emi = P / n;
    } else {
        const factor = Math.pow(1 + r, n);
        emi = (P * r * factor) / (factor - 1);
    }

    const totalPayment = emi * n;
    const totalInterest = totalPayment - P;

    emiEl.textContent = formatINR(emi);
    totalInterestEl.textContent = formatINR(totalInterest);
    totalPaymentEl.textContent = formatINR(totalPayment);

    updateChart(P, totalInterest);
}

const ctx = document.getElementById('paymentChart');

const rootStyles = getComputedStyle(document.documentElement);

const chartFont = rootStyles.getPropertyValue('--font-21').trim();
const tooltipFont = rootStyles.getPropertyValue('--font-18').trim();
const fontFamily = rootStyles.getPropertyValue('--circular').trim();

const fontSizeMatch = tooltipFont.match(/(\d*\.?\d+)rem/);
const fontSize = fontSizeMatch
    ? parseFloat(fontSizeMatch[1]) * parseFloat(rootStyles.fontSize)
    : 14;

const paymentChart = new Chart(ctx, {
    type: 'pie',
    data: {
        labels: ['Principal Loan Amount', 'Total Interest'],
        datasets: [{
            data: [41.73, 58.27],
            backgroundColor: ['#96D020', '#F9B64C'],
            borderColor: '#fff',
            borderWidth: 10,
            spacing: 0
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: { display: false },
            tooltip: {
                enabled: true,
                backgroundColor: '#fff',
                titleColor: '#111',
                bodyColor: '#111',
                borderColor: '#d9d9d9',
                borderWidth: 1,
                padding: 12,
                titleFont: { family: fontFamily, size: fontSize, weight: '400' },
                bodyFont: { family: fontFamily, size: fontSize, weight: '400' },
                callbacks: {
                    title: function (context) {
                        return context[0].label;
                    },
                    label: function (context) {
                        return context.raw + '%';
                    }
                }
            }
        }
    },
    plugins: [{
        id: 'percentageLabels',
        afterDraw(chart) {
            const { ctx, data } = chart;
            const meta = chart.getDatasetMeta(0);

            ctx.save();

            meta.data.forEach((arc, index) => {
                const angle = (arc.startAngle + arc.endAngle) / 2;
                const x = arc.x + Math.cos(angle) * (arc.outerRadius + 25);
                const y = arc.y + Math.sin(angle) * (arc.outerRadius + 25);

                ctx.fillStyle = '#111';
                ctx.font = chartFont;
                ctx.textAlign = x > arc.x ? 'left' : 'right';
                ctx.textBaseline = 'middle';

                ctx.fillText(
                    data.datasets[0].data[index].toFixed(2) + '%',
                    x,
                    y
                );
            });

            ctx.restore();
        }
    }]
});

function updateChart(principal, interest) {
    const total = principal + interest;

    let pPct = 41.73;
    let iPct = 58.27;

    if (total > 0) {
        pPct = (principal / total) * 100;
        iPct = (interest / total) * 100;
    }

    paymentChart.data.datasets[0].data = [
        parseFloat(pPct.toFixed(2)),
        parseFloat(iPct.toFixed(2))
    ];

    paymentChart.update();
}

// Yr/Mo toggle
yrBtn.addEventListener('click', function () {
    tenureUnit = 'yr';
    yrBtn.classList.add('active');
    moBtn.classList.remove('active');
    calculateEMI();
});

moBtn.addEventListener('click', function () {
    tenureUnit = 'mo';
    moBtn.classList.add('active');
    yrBtn.classList.remove('active');
    calculateEMI();
});

// Live recalculation on input
[loanAmountInput, interestRateInput, tenureInput].forEach(function (input) {
    input.addEventListener('input', calculateEMI);
});

// Initial calculation on load (uses placeholder values)
calculateEMI();

</script>

@include('includes.footer')
