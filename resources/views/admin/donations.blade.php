<!-- Clean and minimal donations modals (partial) -->

<!-- All Donations Modal -->
<div id="all-donations-modal" class="modal" role="dialog" aria-modal="true" aria-labelledby="all-donations-title">
    <div class="modal-content" style="max-width: 960px; width: 95%;">
        <div class="modal-header">
            <h3 class="modal-title" id="all-donations-title">All Donations</h3>
            <button class="close-btn" aria-label="Close" onclick="closeModal('all-donations-modal')">&times;</button>
        </div>
        <div class="modal-body">
            <table class="modal-table" aria-describedby="all-donations-caption">
                <caption id="all-donations-caption" class="sr-only">List of all donations</caption>
                <thead>
                    <tr>
                        <th>Donor</th>
                        <th>Method</th>
                        <th>Bags</th>
                        <th>Total Volume (ml)</th>
                        <th>Date</th>
                        <th>Time</th>
                    </tr>
                </thead>
                <tbody id="all-donations-tbody"></tbody>
            </table>
        </div>
    </div>
    <style>.sr-only{position:absolute;width:1px;height:1px;padding:0;margin:-1px;overflow:hidden;clip:rect(0 0 0 0);border:0;}</style>
    <script>
        // Fallback closeModal if not provided by the layout
        if (typeof window.closeModal !== 'function') {
            window.closeModal = function(id){
                var el = document.getElementById(id);
                if (el) el.style.display = 'none';
            };
        }
        function formatTimeFlexible(t){
            if(!t) return 'N/A';
            try{
                var s=t.length===5? t+':00' : t;
                var d=new Date('2000-01-01T'+s);
                if(!isNaN(d.getTime())){
                    return d.toLocaleTimeString('en-US',{hour:'numeric',minute:'2-digit',hour12:true});
                }
            }catch(e){/* ignore */}
            return t;
        }
        function populateAllDonationsModal(items){
            var tbody=document.getElementById('all-donations-tbody');
            if(!tbody) return;
            tbody.innerHTML='';
            if(!items || !items.length){
                tbody.innerHTML='<tr><td colspan="6">No donations found.</td></tr>';
                return;
            }
            items.forEach(function(x){
                var tr=document.createElement('tr');
                var dateStr=x.donation_date||x.scheduled_date;
                var date = dateStr? new Date(dateStr).toLocaleDateString() : 'N/A';
                var method=(x.donation_type||x.method||'').replace('_',' ')||'N/A';
                var name=x.Full_Name||x.donor_name||x.full_name||'N/A';
                var bags=x.number_of_bags||x.bags||x.Number_Of_Bag||'N/A';
                var vol=(x.total_volume||x.total_volume_donated||x.Volume_Per_Bag||'N/A');
                var time=formatTimeFlexible(x.donation_time||x.scheduled_time||'');
                tr.innerHTML = '<td>'+name+'</td>'+
                               '<td>'+method+'</td>'+
                               '<td>'+bags+'</td>'+
                               '<td>'+vol+'ml</td>'+
                               '<td>'+date+'</td>'+
                               '<td>'+time+'</td>';
                tbody.appendChild(tr);
            });
        }
    </script>
</div>

<!-- Walk-in Donations Modal -->
<div id="walk-in-donations-modal" class="modal" role="dialog" aria-modal="true" aria-labelledby="walk-in-donations-title">
    <div class="modal-content" style="max-width: 960px; width: 95%;">
        <div class="modal-header">
            <h3 class="modal-title" id="walk-in-donations-title">Walk-in Donations</h3>
            <button class="close-btn" aria-label="Close" onclick="closeModal('walk-in-donations-modal')">&times;</button>
        </div>
        <div class="modal-body">
            <table class="modal-table" aria-describedby="walk-in-donations-caption">
                <caption id="walk-in-donations-caption" class="sr-only">List of walk-in donations</caption>
                <thead>
                    <tr>
                        <th>Donor</th>
                        <th>Bags</th>
                        <th>Total Volume (ml)</th>
                        <th>Date</th>
                        <th>Time</th>
                    </tr>
                </thead>
                <tbody id="walk-in-donations-tbody"></tbody>
            </table>
        </div>
    </div>
    <script>
        function populateWalkInDonationsModal(items){
            var tbody=document.getElementById('walk-in-donations-tbody');
            if(!tbody) return;
            tbody.innerHTML='';
            if(!items || !items.length){
                tbody.innerHTML='<tr><td colspan="5">No walk-in donations recorded yet.</td></tr>';
                return;
            }
            items.forEach(function(x){
                var tr=document.createElement('tr');
                var dateStr=x.donation_date||x.scheduled_date;
                var date = dateStr? new Date(dateStr).toLocaleDateString() : 'N/A';
                var name=x.Full_Name||x.donor_name||x.full_name||'N/A';
                var bags=x.number_of_bags||x.bags||x.Number_Of_Bag||'N/A';
                var vol=(x.total_volume||x.total_volume_donated||x.Volume_Per_Bag||'N/A');
                var time=formatTimeFlexible(x.donation_time||x.scheduled_time||'');
                tr.innerHTML = '<td>'+name+'</td>'+
                               '<td>'+bags+'</td>'+
                               '<td>'+vol+'ml</td>'+
                               '<td>'+date+'</td>'+
                               '<td>'+time+'</td>';
                tbody.appendChild(tr);
            });
        }
    </script>
</div>

<!-- Pickup Donations Modal -->
<div id="pickup-donations-modal" class="modal" role="dialog" aria-modal="true" aria-labelledby="pickup-donations-title">
    <div class="modal-content" style="max-width: 960px; width: 95%;">
        <div class="modal-header">
            <h3 class="modal-title" id="pickup-donations-title">Pickup Donations</h3>
            <button class="close-btn" aria-label="Close" onclick="closeModal('pickup-donations-modal')">&times;</button>
        </div>
        <div class="modal-body">
            <table class="modal-table" aria-describedby="pickup-donations-caption">
                <caption id="pickup-donations-caption" class="sr-only">List of pickup donations</caption>
                <thead>
                    <tr>
                        <th>Donor</th>
                        <th>Address</th>
                        <th>Bags</th>
                        <th>Total Volume (ml)</th>
                        <th>Date</th>
                        <th>Time</th>
                    </tr>
                </thead>
                <tbody id="pickup-donations-tbody"></tbody>
            </table>
        </div>
    </div>
    <script>
        function populatePickupDonationsModal(items){
            var tbody=document.getElementById('pickup-donations-tbody');
            if(!tbody) return;
            tbody.innerHTML='';
            if(!items || !items.length){
                tbody.innerHTML='<tr><td colspan="6">No pickup donations found.</td></tr>';
                return;
            }
            items.forEach(function(x){
                var tr=document.createElement('tr');
                var dateStr=x.donation_date||x.scheduled_date;
                var date = dateStr? new Date(dateStr).toLocaleDateString() : 'N/A';
                var name=x.Full_Name||x.donor_name||x.full_name||'N/A';
                var addr=x.pickup_address||x.address||'N/A';
                var bags=x.number_of_bags||x.bags||x.Number_Of_Bag||'N/A';
                var vol=(x.total_volume||x.total_volume_donated||x.Volume_Per_Bag||'N/A');
                var time=formatTimeFlexible(x.donation_time||x.scheduled_time||'');
                tr.innerHTML = '<td>'+name+'</td>'+
                               '<td style="max-width:220px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;" title="'+addr+'">'+addr+'</td>'+
                               '<td>'+bags+'</td>'+
                               '<td>'+vol+'ml</td>'+
                               '<td>'+date+'</td>'+
                               '<td>'+time+'</td>';
                tbody.appendChild(tr);
            });
        }
    </script>
</div>
<!-- Clean and minimal donations modals (no conflicting inline styles/scripts) -->

<!-- All Donations Modal -->
<div id="all-donations-modal" class="modal" role="dialog" aria-modal="true" aria-labelledby="all-donations-title">
    <div class="modal-content" style="max-width: 960px; width: 95%;">
        <div class="modal-header">
            <h3 class="modal-title" id="all-donations-title">All Donations</h3>
            <button class="close-btn" aria-label="Close" onclick="closeModal('all-donations-modal')">&times;</button>
        </div>
        <div class="modal-body">
            <table class="modal-table" aria-describedby="all-donations-caption">
                <caption id="all-donations-caption" class="sr-only">List of all donations</caption>
                <thead>
                    <tr>
                        <th>Donor</th>
                        <th>Method</th>
                        <th>Bags</th>
                        <th>Total Volume (ml)</th>
                        <th>Date</th>
                        <th>Time</th>
                    </tr>
                </thead>
                <tbody id="all-donations-tbody"></tbody>
            </table>
        </div>
    </div>
    <style>.sr-only{position:absolute;width:1px;height:1px;padding:0;margin:-1px;overflow:hidden;clip:rect(0 0 0 0);border:0;}</style>
    <script>
        function formatTimeFlexible(t){
            if(!t) return 'N/A';
            try{
                var s=t.length===5? t+':00' : t;
                var d=new Date('2000-01-01T'+s);
                if(!isNaN(d.getTime())){
                    return d.toLocaleTimeString('en-US',{hour:'numeric',minute:'2-digit',hour12:true});
                }
            }catch(e){/* ignore */}
            return t;
        }
        function populateAllDonationsModal(items){
            var tbody=document.getElementById('all-donations-tbody');
            if(!tbody) return;
            tbody.innerHTML='';
            if(!items || !items.length){
                tbody.innerHTML='<tr><td colspan="6">No donations found.</td></tr>';
                return;
            }
            items.forEach(function(x){
                var tr=document.createElement('tr');
                var dateStr=x.donation_date||x.scheduled_date;
                var date = dateStr? new Date(dateStr).toLocaleDateString() : 'N/A';
                var method=(x.donation_type||x.method||'').replace('_',' ')||'N/A';
                var name=x.Full_Name||x.donor_name||x.full_name||'N/A';
                var bags=x.number_of_bags||x.bags||x.Number_Of_Bag||'N/A';
                var vol=(x.total_volume||x.total_volume_donated||x.Volume_Per_Bag||'N/A');
                var time=formatTimeFlexible(x.donation_time||x.scheduled_time||'');
                tr.innerHTML = '<td>'+name+'</td>'+
                               '<td>'+method+'</td>'+
                               '<td>'+bags+'</td>'+
                               '<td>'+vol+'ml</td>'+
                               '<td>'+date+'</td>'+
                               '<td>'+time+'</td>';
                tbody.appendChild(tr);
            });
        }
    </script>
</div>

<!-- Walk-in Donations Modal -->
<div id="walk-in-donations-modal" class="modal" role="dialog" aria-modal="true" aria-labelledby="walk-in-donations-title">
    <div class="modal-content" style="max-width: 960px; width: 95%;">
        <div class="modal-header">
            <h3 class="modal-title" id="walk-in-donations-title">Walk-in Donations</h3>
            <button class="close-btn" aria-label="Close" onclick="closeModal('walk-in-donations-modal')">&times;</button>
        </div>
        <div class="modal-body">
            <table class="modal-table" aria-describedby="walk-in-donations-caption">
                <caption id="walk-in-donations-caption" class="sr-only">List of walk-in donations</caption>
                <thead>
                    <tr>
                        <th>Donor</th>
                        <th>Bags</th>
                        <th>Total Volume (ml)</th>
                        <th>Date</th>
                        <th>Time</th>
                    </tr>
                </thead>
                <tbody id="walk-in-donations-tbody"></tbody>
            </table>
        </div>
    </div>
    <script>
        function populateWalkInDonationsModal(items){
            var tbody=document.getElementById('walk-in-donations-tbody');
            if(!tbody) return;
            tbody.innerHTML='';
            if(!items || !items.length){
                tbody.innerHTML='<tr><td colspan="5">No walk-in donations recorded yet.</td></tr>';
                return;
            }
            items.forEach(function(x){
                var tr=document.createElement('tr');
                var dateStr=x.donation_date||x.scheduled_date;
                var date = dateStr? new Date(dateStr).toLocaleDateString() : 'N/A';
                var name=x.Full_Name||x.donor_name||x.full_name||'N/A';
                var bags=x.number_of_bags||x.bags||x.Number_Of_Bag||'N/A';
                var vol=(x.total_volume||x.total_volume_donated||x.Volume_Per_Bag||'N/A');
                var time=formatTimeFlexible(x.donation_time||x.scheduled_time||'');
                tr.innerHTML = '<td>'+name+'</td>'+
                               '<td>'+bags+'</td>'+
                               '<td>'+vol+'ml</td>'+
                               '<td>'+date+'</td>'+
                               '<td>'+time+'</td>';
                tbody.appendChild(tr);
            });
        }
    </script>
</div>

<!-- Pickup Donations Modal -->
<div id="pickup-donations-modal" class="modal" role="dialog" aria-modal="true" aria-labelledby="pickup-donations-title">
    <div class="modal-content" style="max-width: 960px; width: 95%;">
        <div class="modal-header">
            <h3 class="modal-title" id="pickup-donations-title">Pickup Donations</h3>
            <button class="close-btn" aria-label="Close" onclick="closeModal('pickup-donations-modal')">&times;</button>
        </div>
        <div class="modal-body">
            <table class="modal-table" aria-describedby="pickup-donations-caption">
                <caption id="pickup-donations-caption" class="sr-only">List of pickup donations</caption>
                <thead>
                    <tr>
                        <th>Donor</th>
                        <th>Address</th>
                        <th>Bags</th>
                        <th>Total Volume (ml)</th>
                        <th>Date</th>
                        <th>Time</th>
                    </tr>
                </thead>
                <tbody id="pickup-donations-tbody"></tbody>
            </table>
        </div>
    </div>
    <script>
        function populatePickupDonationsModal(items){
            var tbody=document.getElementById('pickup-donations-tbody');
            if(!tbody) return;
            tbody.innerHTML='';
            if(!items || !items.length){
                tbody.innerHTML='<tr><td colspan="6">No pickup donations found.</td></tr>';
                return;
            }
            items.forEach(function(x){
                var tr=document.createElement('tr');
                var dateStr=x.donation_date||x.scheduled_date;
                var date = dateStr? new Date(dateStr).toLocaleDateString() : 'N/A';
                var name=x.Full_Name||x.donor_name||x.full_name||'N/A';
                var addr=x.pickup_address||x.address||'N/A';
                var bags=x.number_of_bags||x.bags||x.Number_Of_Bag||'N/A';
                var vol=(x.total_volume||x.total_volume_donated||x.Volume_Per_Bag||'N/A');
                var time=formatTimeFlexible(x.donation_time||x.scheduled_time||'');
                tr.innerHTML = '<td>'+name+'</td>'+
                               '<td style="max-width:220px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;" title="'+addr+'">'+addr+'</td>'+
                               '<td>'+bags+'</td>'+
                               '<td>'+vol+'ml</td>'+
                               '<td>'+date+'</td>'+
                               '<td>'+time+'</td>';
                tbody.appendChild(tr);
            });
        }
    </script>
</div>

