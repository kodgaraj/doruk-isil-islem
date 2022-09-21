<table>
  <thead>
    <tr>
      @foreach($siparisBasliklari as $baslik)
        <th>{{ $baslik }}</th>
      @endforeach
      @foreach($islemBasliklari as $baslik)
        <td>{{ $baslik }}</td>
      @endforeach
    </tr>
  </thead>
  <tbody>
    @foreach($siparisler as $siparis)
      @foreach($siparis["islemler"] as $iIndex => $islem)
        <tr>
          @foreach($siparisKeyleri as $key)
            <td>
              @if ($iIndex === 0)
                {{ $siparis[$key] }}
              @endif
            </td>
          @endforeach
          @foreach($islemKeyleri as $key)
            <td>{{ $islem[$key] }}</td>
          @endforeach
        </tr>
      @endforeach
    @endforeach
  </tbody>
</table>
