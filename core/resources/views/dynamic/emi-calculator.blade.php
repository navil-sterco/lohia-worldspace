@include('includes.header')

<x-menu />

<section class="apply_sec">
    <div class="container-lg">
        <div class="col-lg-8">
            <div class="sec_title">
                <h1 class="title21">EMI Calculator</h1>
                <h2 class="title48">LOREM IPSUM DOLOR SIT AMET, CONSECTETUER ADIPISCING ELIT.</h2>
            </div>
        </div>
    </div>

    <div class="container-lg emi_calc_wrapper">
        <div class="row">
            <!-- Left: Input Form -->
            <div class="col-lg-3 col-md-4 p-0">
                <div class="emi_form_box">
                    <div class="emi_form_head">EMI Calculator</div>
                    <div class="emi_form_body">

                        <div class="form_group">
                            <label>Loan Amount</label>
                            <div class="input_with_icon">
                                <span class="icon">₹</span>
                                <input type="text" id="loanAmount" value="25,00,000" inputmode="numeric">
                            </div>
                        </div>

                        <div class="form_group">
                            <label>Interest Rate</label>
                            <div class="input_with_icon">
                                <span class="icon">%</span>
                                <input type="text" id="interestRate" value="8.5" inputmode="decimal">
                            </div>
                        </div>

                        <div class="form_group">
                            <label>Loan Tenure</label>
                            <div class="input_with_toggle">
                                <span class="icon"><i class="fa fa-clock"></i></span>
                                <input type="text" id="loanTenure" value="20" inputmode="numeric">
                                <div class="toggle_btns">
                                    <button type="button" id="tenureYr" class="active">Yr</button>
                                    <button type="button" id="tenureMo">Mo</button>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>

            <!-- Middle: Results -->
            <div class="col-lg-5 col-md-4">
                <div class="emi_results_box">
                    <div class="result_item">
                        <p class="result_label">Loan EMI</p>
                        <h3 class="result_value" id="resultEmi">₹ 0</h3>
                    </div>
                    <hr>
                    <div class="result_item">
                        <p class="result_label">Total Interest Payable</p>
                        <h3 class="result_value" id="resultInterest">₹ 0</h3>
                    </div>
                    <hr>
                    <div class="result_item">
                        <p class="result_label">Total of Payments<br><small>(Principal + Interest)</small></p>
                        <h3 class="result_value" id="resultTotal">₹ 0</h3>
                    </div>
                </div>
            </div>

            <!-- Right: Chart -->
            <div class="col-lg-4 col-md-4">
                <div class="emi_chart_box">
                    <h4 class="chart_title">Break-up of Total Payment</h4>
                    <canvas id="emiPieChart" width="300" height="300"></canvas>
                    <div class="chart_legend">
                        <span><i class="legend_dot green"></i> Principal Loan Amount</span>
                        <span><i class="legend_dot orange"></i> Total Interest</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>


<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js"></script>
<script>
    (function() {
        const loanAmountEl = document.getElementById('loanAmount');
        const interestEl = document.getElementById('interestRate');
        const tenureEl = document.getElementById('loanTenure');
        const tenureYrBtn = document.getElementById('tenureYr');
        const tenureMoBtn = document.getElementById('tenureMo');

        const resultEmi = document.getElementById('resultEmi');
        const resultInterest = document.getElementById('resultInterest');
        const resultTotal = document.getElementById('resultTotal');

        let tenureUnit = 'yr'; // 'yr' or 'mo'

        // Indian number formatting (e.g. 2,50,00,000)
        function formatINR(num) {
            num = Math.round(num);
            let isNegative = num < 0;
            num = Math.abs(num);
            let numStr = num.toString();
            let lastThree = numStr.substring(numStr.length - 3);
            let otherNumbers = numStr.substring(0, numStr.length - 3);
            if (otherNumbers !== '') {
                lastThree = ',' + lastThree;
            }
            let formatted = otherNumbers.replace(/\B(?=(\d{2})+(?!\d))/g, ',') + lastThree;
            return (isNegative ? '-' : '') + '₹ ' + formatted;
        }

        function parseNumber(val) {
            if (!val) return 0;
            return parseFloat(val.toString().replace(/,/g, '')) || 0;
        }

        // Format loan amount input with commas as user types (on keyup)
        function formatInputWithCommas(el) {
            let raw = parseNumber(el.value);
            let cursorFromEnd = el.value.length - el.selectionStart;
            el.value = raw ? raw.toLocaleString('en-IN') : '';
            let newPos = el.value.length - cursorFromEnd;
            el.setSelectionRange(newPos, newPos);
        }

        let chart;

        function initChart(principal, interest) {
            const ctx = document.getElementById('emiPieChart').getContext('2d');
            if (chart) {
                chart.data.datasets[0].data = [principal, interest];
                chart.update();
                return;
            }
            chart = new Chart(ctx, {
                type: 'pie',
                data: {
                    labels: ['Principal Loan Amount', 'Total Interest'],
                    datasets: [{
                        data: [principal, interest],
                        backgroundColor: ['#8bc34a', '#f5a623'],
                        borderWidth: 0
                    }]
                },
                options: {
                    responsive: false,
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            callbacks: {
                                label: function(ctx) {
                                    let total = ctx.dataset.data.reduce((a, b) => a + b, 0);
                                    let pct = total ? ((ctx.raw / total) * 100).toFixed(2) : 0;
                                    return ctx.label + ': ' + pct + '%';
                                }
                            }
                        },
                        datalabels: false
                    }
                }
            });
        }

        function calculateEMI() {
            const P = parseNumber(loanAmountEl.value);
            const annualRate = parseFloat(interestEl.value) || 0;
            let tenureVal = parseNumber(tenureEl.value);

            let n = tenureUnit === 'yr' ? tenureVal * 12 : tenureVal; // months
            let r = annualRate / 12 / 100; // monthly rate

            let emi = 0;
            if (P > 0 && r > 0 && n > 0) {
                emi = (P * r * Math.pow(1 + r, n)) / (Math.pow(1 + r, n) - 1);
            } else if (P > 0 && n > 0) {
                emi = P / n;
            }

            const totalPayment = emi * n;
            const totalInterest = totalPayment - P;

            resultEmi.textContent = formatINR(emi);
            resultInterest.textContent = formatINR(totalInterest > 0 ? totalInterest : 0);
            resultTotal.textContent = formatINR(totalPayment > 0 ? totalPayment : 0);

            initChart(P, totalInterest > 0 ? totalInterest : 0);
        }

        // Trigger on every keyup
        loanAmountEl.addEventListener('keyup', function() {
            formatInputWithCommas(loanAmountEl);
            calculateEMI();
        });
        interestEl.addEventListener('keyup', calculateEMI);
        tenureEl.addEventListener('keyup', calculateEMI);

        tenureYrBtn.addEventListener('click', function() {
            tenureUnit = 'yr';
            tenureYrBtn.classList.add('active');
            tenureMoBtn.classList.remove('active');
            calculateEMI();
        });
        tenureMoBtn.addEventListener('click', function() {
            tenureUnit = 'mo';
            tenureMoBtn.classList.add('active');
            tenureYrBtn.classList.remove('active');
            calculateEMI();
        });

        // Initial calculation on page load
        formatInputWithCommas(loanAmountEl);
        calculateEMI();
    })();
</script>

@include('includes.footer')
