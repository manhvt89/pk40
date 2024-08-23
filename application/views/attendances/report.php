<?php $this->load->view("partial/header"); ?>
<?php
    $sallary_per_hour = 0;
    if(!is_float($this->config->item('sallary_per_hour')))
    {
        $sallary_per_hour = 15000;
    } else {
        $sallary_per_hour = $this->config->item('sallary_per_hour');
    }
?>
<h1>Báo cáo chấm công</h1>
    <table border="1">
        <tr>
            <th>Employee Name</th>
            <th>Total Second Worked</th>
            <th>Total Hours Worked</th>
            <th>Amount Earned</th>
        </tr>
        <?php foreach ($report as $row): ?>
            <tr>
                <td><?= $row->last_name . ' '.$row->first_name; ?></td>
                <td><?= $row->total_minutes; ?></td>
                <td><?= round($row->total_minutes / 3600, 2); ?></td>
                <td><?= round($row->total_minutes / 3600 * $sallary_per_hour, 2); // Giả sử lương là 15 đồng/h ?></td>
            </tr>
        <?php endforeach; ?>
    </table>

<?php $this->load->view("partial/footer"); ?>
