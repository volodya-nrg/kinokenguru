<p>Здравствуйте, к Вашему вниманию хотим посоветовать посмотреть фильмы, каторые были добавлены не давно на сайт <a href="http://kinokenguru.ru">http://kinokenguru.ru</a> .</p>

<ul>
	@foreach($aProducts as $product)
		<li>
			<a href='{{ url($product->slug) }}'>{{ $product->name }}</a>
		</li>
	@endforeach
</ul>
