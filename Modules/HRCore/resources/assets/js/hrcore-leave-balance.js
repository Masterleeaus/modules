/**
 * Leave Balance Dashboard
 */

'use strict';

(function () {
  // Get chart data from the page
  const chartData = window.leaveBalanceData || {
    labels: [],
    entitlement: [],
    used: [],
    available: []
  };

  // Chart Colors
  const chartColors = {
    primary: '#696cff',
    success: '#71dd37',
    warning: '#ffab00',
    danger: '#ff3e1d',
    info: '#03c3ec',
    secondary: '#8592a3'
  };

  // Initialize Leave Balance Chart
  const leaveBalanceChartEl = document.querySelector('#leaveBalanceChart');
  if (leaveBalanceChartEl) {
    const leaveBalanceChartOptions = {
      series: [{
        name: 'Used',
        data: chartData.used
      }, {
        name: 'Available',
        data: chartData.available
      }],
      chart: {
        type: 'bar',
        height: 300,
        stacked: true,
        toolbar: {
          show: false
        }
      },
      plotOptions: {
        bar: {
          horizontal: true,
          barHeight: '60%',
          dataLabels: {
            total: {
              enabled: true,
              offsetX: 0,
              style: {
                fontSize: '13px',
                fontWeight: 600
              }
            }
          }
        }
      },
      colors: [chartColors.danger, chartColors.success],
      dataLabels: {
        enabled: true,
        formatter: function (val, opt) {
          return val > 0 ? val : '';
        },
        style: {
          fontSize: '12px',
          colors: ['#fff']
        }
      },
      stroke: {
        width: 1,
        colors: ['#fff']
      },
      xaxis: {
        categories: chartData.labels,
        labels: {
          formatter: function (val) {
            return val + ' days';
          }
        }
      },
      yaxis: {
        title: {
          text: undefined
        }
      },
      tooltip: {
        y: {
          formatter: function (val) {
            return val + ' days';
          }
        }
      },
      fill: {
        opacity: 1
      },
      legend: {
        position: 'top',
        horizontalAlign: 'left',
        offsetX: 40
      },
      grid: {
        borderColor: '#f1f1f1',
        xaxis: {
          lines: {
            show: true
          }
        },
        yaxis: {
          lines: {
            show: false
          }
        }
      }
    };

    const leaveBalanceChart = new ApexCharts(leaveBalanceChartEl, leaveBalanceChartOptions);
    leaveBalanceChart.render();
  }

  // Apply Leave button animation
  const applyLeaveBtn = document.querySelector('.btn-primary');
  if (applyLeaveBtn) {
    applyLeaveBtn.addEventListener('mouseenter', function() {
      this.classList.add('shadow-sm');
    });
    
    applyLeaveBtn.addEventListener('mouseleave', function() {
      this.classList.remove('shadow-sm');
    });
  }

  // Progress bars animation on scroll
  const progressBars = document.querySelectorAll('.progress-bar');
  const animateProgressBars = () => {
    progressBars.forEach(bar => {
      const rect = bar.getBoundingClientRect();
      if (rect.top >= 0 && rect.bottom <= window.innerHeight) {
        const width = bar.style.width;
        bar.style.width = '0%';
        setTimeout(() => {
          bar.style.transition = 'width 1s ease-in-out';
          bar.style.width = width;
        }, 100);
      }
    });
  };

  // Trigger animation on page load
  window.addEventListener('load', animateProgressBars);

  // Add tooltips to progress bars
  progressBars.forEach(bar => {
    const percentage = bar.getAttribute('aria-valuenow');
    bar.setAttribute('data-bs-toggle', 'tooltip');
    bar.setAttribute('data-bs-placement', 'top');
    bar.setAttribute('title', `${percentage}% used`);
  });

  // Initialize Bootstrap tooltips
  const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
  tooltipTriggerList.map(function (tooltipTriggerEl) {
    return new bootstrap.Tooltip(tooltipTriggerEl);
  });

  // Table row hover effect
  const tableRows = document.querySelectorAll('tbody tr');
  tableRows.forEach(row => {
    row.addEventListener('mouseenter', function() {
      this.style.backgroundColor = '#f8f9fa';
      this.style.cursor = 'pointer';
    });
    
    row.addEventListener('mouseleave', function() {
      this.style.backgroundColor = '';
    });
  });

  // Export functionality (placeholder)
  const exportBtn = document.querySelector('#exportBalance');
  if (exportBtn) {
    exportBtn.addEventListener('click', function() {
      // Implement export functionality
      Swal.fire({
        icon: 'info',
        title: 'Export Leave Balance',
        text: 'Export functionality will be implemented here.',
        customClass: {
          confirmButton: 'btn btn-primary'
        },
        buttonsStyling: false
      });
    });
  }
})();