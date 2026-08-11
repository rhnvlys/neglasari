$baseUrl = "https://neglasari-pi.vercel.app";
$users = @(
    @{ name = "superadmin"; pass = "password123"; target = "/admin/dashboard" },
    @{ name = "admin"; pass = "password123"; target = "/admin/dashboard" },
    @{ name = "kades"; pass = "password123"; target = "/kades/dashboard" },
    @{ name = "pegawai"; pass = "password123"; target = "/pegawai/dashboard" }
);

foreach ($u in $users) {
    $sess = $null;
    $r1 = Invoke-WebRequest -Uri "$baseUrl/login" -SessionVariable sess;
    $match = [regex]::Match($r1.Content, 'name="_token"\s+value="([^"]+)"');
    $token = $match.Groups[1].Value;
    
    $body = @{ _token = $token; login = $u.name; password = $u.pass };
    $r2 = Invoke-WebRequest -Uri "$baseUrl/login" -Method Post -Body $body -WebSession $sess -MaximumRedirection 0 -ErrorAction SilentlyContinue;
    
    $status = $r2.StatusCode;
    $loc = $r2.Headers.Location;
    
    if ($status -eq 302) {
        $r3 = Invoke-WebRequest -Uri "$baseUrl$loc" -WebSession $sess -ErrorAction SilentlyContinue;
        Write-Host "USER: $($u.name) -> LOGIN OK (302 -> $loc), TARGET STATUS: $($r3.StatusCode)";
    } else {
        Write-Host "USER: $($u.name) -> FAILED ($status)";
    }
}
