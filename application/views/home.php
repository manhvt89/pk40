<?php $this->load->view("partial/header"); ?>
<style>
    .box {
      border: 1px solid #ddd;
      padding: 20px;
      margin: 10px;
      border-radius: 5px;
      background-color: #f9f9f9;
      text-align: center;
    }

    .box h4 {
      font-size: 18px;
      color: #333;
    }

    .box p {
      font-size: 22px;
      font-weight: bold;
      color: #4CAF50;
    }

    .dashboard-summary, .sales-performance, .inventory-status, .order-list {
      margin-bottom: 40px;
    }

    .table th, .table td {
      text-align: center;
    }

    .chart-container {
      width: 100%;
      height: 400px;
      margin-top: 40px;
      margin-bottom: 40px;
    }
  </style>
<div class="container-fluid">
  <!-- Tổng quan doanh thu -->
  <div class="row dashboard-summary">
    <div class="col-md-3">
      <div class="box">
        <h4>Doanh thu hôm nay</h4>
        <p><?php echo $today?></p>
      </div>
    </div>
    <div class="col-md-3">
      <div class="box">
        <h4>Doanh thu tuần này</h4>
        <p><?=$thisWeek?></p>
      </div>
    </div>
    <div class="col-md-3">
      <div class="box">
        <h4>Doanh thu tháng này</h4>
        <p><?=$thisMonth?></p>
      </div>
    </div>
    <div class="col-md-3">
      <div class="box">
        <h4>Tổng số đơn hàng</h4>
        <p>150</p>
      </div>
    </div>
  </div>

  <!-- Biểu đồ doanh thu theo ngày trong tháng hiện tại -->
  <div class="chart-container">
    <h3>Doanh thu theo ngày trong tháng hiện tại</h3>
    <canvas id="revenueChart"></canvas>
  </div>

  <!-- Danh sách đơn hàng -->
  <div class="order-list">
    <h3>Danh sách đơn hàng hôm nay</h3>
    <table class="table table-bordered">
      <thead>
        <tr>
          <th>ID Đơn hàng</th>
          <th>Tên khách hàng</th>
          <th>Ngày đặt</th>
          <th>Tổng giá trị</th>
          <th>Người bán</th>
        </tr>
      </thead>
      <tbody>
      <?php if(!empty($summary_sales)):?>
      <?php foreach($summary_sales as $sale): ?>  
        <tr>
          <td>#<?=$sale['id']?></td>
          <td><?=$sale['customer_name']?></td>
          <td><?=$sale['sale_date']?></td>
          <td><?=$sale['total']?></td>
          <td><?=$sale['employee_name']?></td>
        </tr>
      <?php endforeach; ?>
      <?php else: ?>
        <tr>
          <td colspan="5">
            Chưa có đơn hàng
          </td>
        </tr>
      <?php endif;?>
      </tbody>
    </table>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
  // Dữ liệu doanh thu ngẫu nhiên cho mỗi ngày trong tháng
  const labels = <?=$labels?>; // Ngày trong tháng
  const data = <?=$revenues?>; // Doanh thu ngẫu nhiên

  const ctx = document.getElementById('revenueChart').getContext('2d');
  const revenueChart = new Chart(ctx, {
    type: 'line',
    data: {
      labels: labels,
      datasets: [{
        label: 'Doanh thu theo ngày',
        data: data,
        borderColor: '#4CAF50',
        fill: false
      }]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      scales: {
        x: {
          title: {
            display: true,
            text: 'Ngày'
          }
        },
        y: {
          title: {
            display: true,
            text: 'Doanh thu (VNĐ)'
          },
          beginAtZero: true
        }
      }
    }
  });
</script>

<?php $this->load->view("partial/footer"); ?>