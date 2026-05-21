<?php
// 初期値の設定（デフォルト値）
$initial_gdp = isset($_POST['initial_gdp']) ? (float)$_POST['initial_gdp'] : 500;  // 初期GDP
$growth_rate = isset($_POST['growth_rate']) ? (float)$_POST['growth_rate'] : 1.0;   // 民間の自然成長率 (%)
$gov_spend   = isset($_POST['gov_spend'])   ? (float)$_POST['gov_spend']   : 10;    // 毎年の政府支出
$multiplier  = isset($_POST['multiplier'])  ? (float)$_POST['multiplier']  : 1.5;   // 乗数効果（1.0〜2.0程度が一般的）
$years       = isset($_POST['years'])       ? (int)$_POST['years'] : 10;                 // シミュレーション年数

$simulation_results = [];

// シミュレーション計算の実行
$current_gdp = $initial_gdp;

for ($i = 0; $i <= $years; $i++) {
    if ($i === 0) {
        $simulation_results[$i] = [
            'gdp' => $current_gdp,
            'gov_total' => 0
        ];
    } else {
        // 1. まず民間ベースの自然成長
        $current_gdp = $current_gdp * (1 + ($growth_rate / 100));
        
        // 2. 政府支出による押し上げ効果（政府支出 × 乗数）
        $gov_effect = $gov_spend * $multiplier;
        $current_gdp += $gov_effect;
        
        $simulation_results[$i] = [
            'gdp' => $current_gdp,
            'gov_total' => $gov_spend * $i // 政府が累計でいくら投資したか
        ];
    }
}
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>高度なGDPシミュレーター（政府支出対応）</title>
    <style>
        body { font-family: sans-serif; margin: 40px; background: #f4f6f9; color: #333; }
        .container { max-width: 700px; background: white; padding: 30px; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); }
        h2 { border-bottom: 2px solid #007bff; padding-bottom: 10px; margin-top: 0; }
        .form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 20px; }
        .form-group { display: flex; flex-direction: column; }
        .form-group.full-width { grid-column: span 2; }
        label { margin-bottom: 5px; font-weight: bold; font-size: 0.9em; }
        input[type="number"] { padding: 10px; border: 1px solid #ccc; border-radius: 4px; font-size: 1fr; }
        button { background: #28a745; color: white; border: none; padding: 12px; border-radius: 4px; cursor: pointer; font-size: 1em; font-weight: bold; }
        button:hover { background: #218838; }
        table { width: 100%; border-collapse: collapse; margin-top: 25px; }
        th, td { border: 1px solid #dee2e6; padding: 12px; text-align: right; }
        th { background: #e9ecef; text-align: center; }
        tr:nth-child(even) { background: #f8f9fa; }
        .alert { background: #e2f0d9; padding: 15px; border-radius: 4px; margin-top: 20px; font-size: 0.9em; line-height: 1.5; }
    </style>
</head>
<body>

<div class="container">
    <h2>GDP推移シミュレーター（政府支出・乗数効果モデル）</h2>
    
    <form method="post" action="">
        <div class="form-grid">
            <div class="form-group">
                <label for="initial_gdp">初期GDP（基準値）</label>
                <input type="number" id="initial_gdp" name="initial_gdp" step="0.1" value="<?php echo htmlspecialchars($initial_gdp); ?>" required>
            </div>
            <div class="form-group">
                <label for="growth_rate">民間の自然成長率 (%)</label>
                <input type="number" id="growth_rate" name="growth_rate" step="0.1" value="<?php echo htmlspecialchars($growth_rate); ?>" required>
            </div>
            <div class="form-group">
                <label for="gov_spend">毎年の追加政府支出（投資）</label>
                <input type="number" id="gov_spend" name="gov_spend" step="0.1" value="<?php echo htmlspecialchars($gov_spend); ?>" required>
            </div>
            <div class="form-group">
                <label for="multiplier">乗数効果（倍率）</label>
                <input type="number" id="multiplier" name="multiplier" step="0.1" min="0" max="5" value="<?php echo htmlspecialchars($multiplier); ?>" required>
            </div>
            <div class="form-group full-width">
                <label for="years">シミュレーション年数</label>
                <input type="number" id="years" name="years" min="1" max="50" value="<?php echo htmlspecialchars($years); ?>" required>
            </div>
        </div>
        <button type="submit" style="width: 100%;">経済シミュレーションを実行</button>
    </form>

    <h3>シミュレーション結果</h3>
    <table>
        <thead>
            <tr>
                <th style="text-align: center;">経過年数</th>
                <th>予測GDP</th>
                <th>政府投資累計</th>
                <th>初期比</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($simulation_results as $year => $data): ?>
                <tr>
                    <td style="text-align: center;"><?php echo $year === 0 ? '初期状態' : $year . '年後'; ?></td>
                    <td><?php echo number_format($data['gdp'], 2); ?></td>
                    <td><?php echo number_format($data['gov_total'], 2); ?></td>
                    <td><?php echo number_format(($data['gdp'] / $initial_gdp) * 100, 1); ?>%</td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <div class="alert">
        <strong>💡 シミュレーションのポイント:</strong><br>
        政府支出を「0」にすると純粋な民間成長だけの推移になります。政府支出や乗数を引き上げると、投資額以上のスピードでGDPの合計が押し上げられる「乗数効果」のパワーを確認できます。
    </div>
</div>

</body>
</html>
