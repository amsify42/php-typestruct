<?php

namespace Amsify42\Tests\TypeStruct;

export typestruct Simple {
	name: ?string,
	detail : ?{
		id: int,
		name: ?string
	}
}