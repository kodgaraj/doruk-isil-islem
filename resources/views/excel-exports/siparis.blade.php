@foreach($siparisler as $siparis)
  <table>
    <thead>
      <tr>
        @foreach($siparisBasliklari as $baslik)
          <th>{{ $baslik }}</th>
        @endforeach
      </tr>
    </thead>
    <tbody>
      <tr>
        <td>
          {{ $siparis["siparisId"] }}
        </td>
        <td>
          {{ $siparis["siparisNo"] }}
        </td>
        <td>
          {{ $siparis["gecenSure"] }} Gün
        </td>
        <td>
          {{ $siparis["firmaAdi"] }}
        </td>
        <td>
          {{ $siparis["islemSayisi"] }}
        </td>
        <td>
          {{ $siparis["netYazi"] }}
        </td>
        @can("siparis_ucreti_goruntuleme")
          <td>
            {{ $siparis["tutarTLYazi"] }}
          </td>
          <td>
            {{ $siparis["tutarUSDYazi"] }}
          </td>
        @endcan
        <td>
          {{ $siparis["tarih"] }}
        </td>
      </tr>
      <tr style="border: 1px solid black;">
        @foreach($islemBasliklari as $baslik)
          <td>{{ $baslik }}</td>
        @endforeach
      </tr>
      @foreach($siparis["islemler"] as $islem)
        <tr>
          @foreach($islemKeyleri as $key)
            <td>{{ $islem[$key] }}</td>
          @endforeach
        </tr>
      @endforeach
      {{-- <tr>
        <td colspan="100%">{{ $drawing }}</td>
      </tr> --}}
    </tbody>
  </table>
@endforeach
