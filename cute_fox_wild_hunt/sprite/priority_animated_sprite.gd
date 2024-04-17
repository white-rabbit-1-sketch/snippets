extends AnimatedSprite

class_name PriorityAnimatedSprite

var played_animation_priority: int = 0
var priority_animation: String

func reset() -> void:
	played_animation_priority = 0

func priority_queue(anim: String, priority: int) -> void:	
	if priority >= played_animation_priority:
		played_animation_priority = priority
		priority_animation = anim

func priority_play() -> void:
	self.play(priority_animation)
