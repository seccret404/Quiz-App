// Import Chart.js
import {
  Chart, BarController, BarElement, LinearScale, CategoryScale, Tooltip, Legend,
} from 'chart.js';

// Import utilities
import { getCssVariable } from '../utils';

Chart.register(BarController, BarElement, LinearScale, CategoryScale, Tooltip, Legend);

// A chart built with Chart.js 3
// https://www.chartjs.org/
const dashboardCard11 = () => {
  const ctx = document.getElementById('dashboard-card-11');
  if (!ctx) return;

  const darkMode = localStorage.getItem('dark-mode') === 'true';

  const tooltipBodyColor = {
    light: '#6B7280',
    dark: '#9CA3AF'
  };

  const tooltipBgColor = {
    light: '#ffffff',
    dark: '#374151'
  };

  const tooltipBorderColor = {
    light: '#E5E7EB',
    dark: '#4B5563'
  };

  fetch('/json-data-feed?datatype=10')
  .then(a => {
      return a.json();
    })
    .then(result => {

      // Calculate sum of values
      const reducer = (accumulator, currentValue) => accumulator + currentValue
      const max = result.data.reduce(reducer)         

      const dataset1 = result.data.splice(0, 1);
      const dataset2 = result.data.splice(0, 1);
      const dataset3 = result.data.splice(0, 1);
      const dataset4 = result.data.splice(0, 1);
      const dataset5 = result.data;      

      const chart = new Chart(ctx, {
        type: 'bar',
        data: {
          labels: result.labels,
          datasets: [
            {
              label: 'Having difficulties using the product',
              data: dataset1,
              backgroundColor: getCssVariable('--color-violet-500'),
              hoverBackgroundColor: getCssVariable('--color-violet-600'),
              barPercentage: 1,
              categoryPercentage: 1,
            },
            {
              label: 'Missing features I need',
              data: dataset2,
              backgroundColor: getCssVariable('--color-violet-700'),
              hoverBackgroundColor: getCssVariable('--color-violet-800'),
              barPercentage: 1,
              categoryPercentage: 1,
            },
            {
              label: 'Not satisfied about the quality of the product',
              data: dataset3,
              backgroundColor: getCssVariable('--color-sky-500'),
              hoverBackgroundColor: getCssVariable('--color-sky-600'),
              barPercentage: 1,
              categoryPercentage: 1,
            },
            {
              label: 'The product doesn’t look as advertised',
              data: dataset4,
              backgroundColor: getCssVariable('--color-green-500'),
              hoverBackgroundColor: getCssVariable('--color-green-600'),
              barPercentage: 1,
              categoryPercentage: 1,
            },
            {
              label: 'Other',
              data: dataset5,
              backgroundColor: getCssVariable('--color-gray-200'),
              hoverBackgroundColor: getCssVariable('--color-gray-300'),
              barPercentage: 1,
              categoryPercentage: 1,
            },            
          ],
        },
        options: {
          indexAxis: 'y',
          layout: {
            padding: {
              top: 12,
              bottom: 12,
              left: 20,
              right: 20,
            },
          },
          scales: {
            x: {
              stacked: true,
              display: false,
              max: max,
            },
            y: {
              stacked: true,
              display: false,
            },
          },
          plugins: {
            legend: {
              display: false,
            },
            htmlLegend: {
              // ID of the container to put the legend in
              containerID: 'dashboard-card-11-legend',
            },            
            tooltip: {
              callbacks: {
                title: () => false, // Disable tooltip title
                label: (context) => context.parsed.x,
              },
              bodyColor: darkMode ? tooltipBodyColor.dark : tooltipBodyColor.light,
              backgroundColor: darkMode ? tooltipBgColor.dark : tooltipBgColor.light,
              borderColor: darkMode ? tooltipBorderColor.dark : tooltipBorderColor.light,               
            },
          },
          interaction: {
            intersect: false,
            mode: 'nearest'
          },
          animation: {
            duration: 500,
          },
          maintainAspectRatio: false,
          resizeDelay: 200,
        },
        plugins: [{
          id: 'htmlLegend',
          afterUpdate(c, args, options) {
            const legendContainer = document.getElementById(options.containerID);
            const ul = legendContainer.querySelector('ul');
            if (!ul) return;
            // Remove old legend items
            while (ul.firstChild) {
              ul.firstChild.remove()
            }
            // Reuse the built-in legendItems generator
            const items = c.options.plugins.legend.labels.generateLabels(c)
            items.forEach((item) => {
              const li = document.createElement('li')
              li.style.display = 'flex'
              li.style.justifyContent = 'space-between'
              li.style.alignItems = 'center'
              li.style.paddingTop = '10px'
              li.style.paddingBottom = '10px'
              const wrapper = document.createElement('div')
              wrapper.style.display = 'flex'
              wrapper.style.alignItems = 'center'
              const box = document.createElement('div')
              box.style.width = '12px'
              box.style.height = '12px'
              box.style.borderRadius = '4px'
              box.style.marginRight = '12px'
              box.style.backgroundColor = item.fillStyle
              const label = document.createElement('div')
              const value = document.createElement('div')
              value.style.fontWeight = '500'
              value.style.marginLeft = '12px'
              value.style.color = item.text === 'Other' ? getCssVariable('--color-gray-400') : item.fillStyle
              const theValue = c.data.datasets[item.datasetIndex].data.reduce((a, b) => a + b, 0)
              const valueText = document.createTextNode(`${parseInt(theValue / max * 100)}%`)
              const labelText = document.createTextNode(item.text)
              value.appendChild(valueText)
              label.appendChild(labelText)
              ul.appendChild(li)
              li.appendChild(wrapper)
              li.appendChild(value)
              wrapper.appendChild(box)
              wrapper.appendChild(label)
            })
          },
        }],  
      });

      document.addEventListener('darkMode', (e) => {
        const { mode } = e.detail;
        if (mode === 'on') {
          chart.options.plugins.tooltip.bodyColor = tooltipBodyColor.dark;
          chart.options.plugins.tooltip.backgroundColor = tooltipBgColor.dark;
          chart.options.plugins.tooltip.borderColor = tooltipBorderColor.dark;
        } else {
          chart.options.plugins.tooltip.bodyColor = tooltipBodyColor.light;
          chart.options.plugins.tooltip.backgroundColor = tooltipBgColor.light;
          chart.options.plugins.tooltip.borderColor = tooltipBorderColor.light;
        }
        chart.update('none');
      });       
    });
};

export default dashboardCard11;
